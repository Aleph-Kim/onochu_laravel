# 확장 모듈과 Apache 설정을 담는 베이스 (builder, 최종 이미지가 공유)
FROM php:8.3-apache AS base

# 한국 시간대 설정
ENV TZ=Asia/Seoul
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# 필요한 PHP 확장 모듈 설치 (gd, mbstring 빌드에 필요한 라이브러리 포함)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

# mod_rewrite, mod_remoteip 모듈 활성화
RUN a2enmod rewrite remoteip

# DocumentRoot를 Laravel public 폴더로 변경
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf


# 의존성 설치와 프론트엔드 빌드를 담당하는 빌더
FROM base AS builder

# 빌드에만 쓰는 도구 설치 (Node.js, Composer 용)
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl unzip git nodejs npm \
    && rm -rf /var/lib/apt/lists/*

# Composer 설치
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# composer 라이브러리 설치 (의존성 파일 먼저 복사해서 캐시 활용)
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-dev --no-scripts --no-autoloader

# npm 라이브러리 설치 (락 파일 먼저 복사해서 캐시 활용)
COPY package.json package-lock.json ./
RUN npm ci

# 애플리케이션 파일 복사
COPY . .

# 오토로더 최적화 (소스가 모두 있는 상태에서 실행)
RUN composer dump-autoload --optimize --no-dev

# Vite 빌드 후 node_modules 제거
RUN npm run build && rm -rf node_modules


# 빌드 결과물만 담은 최종 이미지 (Apache가 80포트로 서빙)
FROM base

WORKDIR /var/www/html

# 빌더에서 완성된 파일만 복사
COPY --from=builder /var/www/html .

# 스토리지, 캐시 폴더 권한 설정
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
