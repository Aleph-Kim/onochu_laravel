# Base Stage: PHP 확장 모듈 설치
FROM php:8.3-fpm AS base

# 한국 시간대 설정
ENV TZ=Asia/Seoul
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Laravel 필수 PHP 확장 모듈 설치
# - libpng-dev: gd 빌드용 / libonig-dev: mbstring 빌드용
#   (런타임 공유 라이브러리 libpng16, libonig5도 함께 설치됨)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*


# Builder Stage: 의존성 설치 및 프론트엔드 빌드
FROM base AS builder

# 빌드 전용 도구 설치 (Node.js: Vite 빌드 / git·unzip: Composer)
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl unzip git nodejs npm \
    && rm -rf /var/lib/apt/lists/*

# Composer 설치
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# 의존성 파일만 먼저 복사하여 레이어 캐시 활용 (소스 변경 시 재설치 방지)
# --no-scripts: 이 시점엔 artisan 파일이 없으므로 post-autoload-dump 스크립트 비활성화
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-dev --no-scripts --no-autoloader

# npm도 동일하게 락 파일 먼저 복사하여 캐시 활용
COPY package.json package-lock.json ./
RUN npm ci

# 소스 전체 복사
COPY . .

# 소스가 모두 있는 상태에서 최적화된 오토로더 생성 (package:discover 정상 실행)
RUN composer dump-autoload --optimize --no-dev

# Vite 프론트엔드 빌드 후 node_modules 제거
RUN npm run build && rm -rf node_modules


# Final Stage: 프로덕션 런타임 이미지
FROM base

WORKDIR /var/www/html

# 빌드 스테이지에서 완성된 파일만 복사 (node_modules 미포함)
COPY --from=builder /var/www/html .

# 스토리지 및 캐시 디렉토리 권한 설정
RUN chown -R www-data:www-data storage bootstrap/cache

# PHP-FPM 기본 포트
EXPOSE 9000
