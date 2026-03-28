const logo = document.getElementById("logo");
const searchFormWrap = document.getElementById("searchFormWrap");
const searchFormHideBtn = document.getElementById("searchFormHideBtn");
const searchForm = document.getElementById("searchForm");
const rightBtnBox = document.getElementById("rightBtnBox");

function showSearchForm() {
    logo.style.display = "none";
    rightBtnBox.style.display = "none";
    searchForm.style.display = "block";
    searchFormHideBtn.style.display = "block";
    searchFormWrap.style.width = "100%";

}

function hiddenSearchForm() {
    logo.style.display = "flex";
    rightBtnBox.style.display = "flex";
    searchForm.style.display = "none";
    searchFormHideBtn.style.display = "none";
    searchFormWrap.style.width = "auto";
}

function confirmBack() {
    if (confirm("페이지를 나가시겠습니까?")) {
        window.history.go(-1);
    }
}

/**
 * 한글 문자열에서 초성, 중성, 종성을 추출하는 함수
 * 
 * @param {string} str - 변환할 문자열
 * @returns {string} - 초성, 중성, 종성이 분리된 문자열
 */
function separateKoreanCharacters(str) {
    // 유니코드 한글 시작 코드: '가'의 유니코드 값
    const HANGUL_START = 0xAC00;
    // 유니코드 한글 끝 코드: '힣'의 유니코드 값 
    const HANGUL_END = 0xD7A3;

    // 초성 배열
    const CHOSUNG = ['ㄱ', 'ㄲ', 'ㄴ', 'ㄷ', 'ㄸ', 'ㄹ', 'ㅁ', 'ㅂ', 'ㅃ', 'ㅅ', 'ㅆ', 'ㅇ', 'ㅈ', 'ㅉ', 'ㅊ', 'ㅋ', 'ㅌ', 'ㅍ', 'ㅎ'];
    // 중성 배열
    const JUNGSUNG = ['ㅏ', 'ㅐ', 'ㅑ', 'ㅒ', 'ㅓ', 'ㅔ', 'ㅕ', 'ㅖ', 'ㅗ', 'ㅘ', 'ㅙ', 'ㅚ', 'ㅛ', 'ㅜ', 'ㅝ', 'ㅞ', 'ㅟ', 'ㅠ', 'ㅡ', 'ㅢ', 'ㅣ'];
    // 종성 배열
    const JONGSUNG = ['', 'ㄱ', 'ㄲ', 'ㄳ', 'ㄴ', 'ㄵ', 'ㄶ', 'ㄷ', 'ㄹ', 'ㄺ', 'ㄻ', 'ㄼ', 'ㄽ', 'ㄾ', 'ㄿ', 'ㅀ', 'ㅁ', 'ㅂ', 'ㅄ', 'ㅅ', 'ㅆ', 'ㅇ', 'ㅈ', 'ㅊ', 'ㅋ', 'ㅌ', 'ㅍ', 'ㅎ'];

    // 한글 전체(자음, 모음, 완성형 한글) 매칭 패턴
    const HANGUL_PATTERN = /[ㄱ-ㅎ|ㅏ-ㅣ|가-힣]/g;
    // 자음만 매칭하는 패턴
    const CONSONANT_PATTERN = /[ㄱ-ㅎ]/;
    // 모음만 매칭하는 패턴
    const VOWEL_PATTERN = /[ㅏ-ㅣ]/;

    // 결과 문자열을 배열로 구성하여 join으로 최종 결합
    let result = [];

    str.replace(HANGUL_PATTERN, (char) => {
        const code = char.charCodeAt(0);

        // 완성형 한글인 경우
        if (code >= HANGUL_START && code <= HANGUL_END) {
            const normalized = code - HANGUL_START;
            const chosungIndex = Math.floor(normalized / (JUNGSUNG.length * JONGSUNG.length));
            const jungsungIndex = Math.floor((normalized % (JUNGSUNG.length * JONGSUNG.length)) / JONGSUNG.length);
            const jongsungIndex = normalized % JONGSUNG.length;

            result.push(
                CHOSUNG[chosungIndex],
                JUNGSUNG[jungsungIndex],
                JONGSUNG[jongsungIndex]
            );
            return;
        }

        // 자음 또는 모음만 있는 경우
        if (CONSONANT_PATTERN.test(char) || VOWEL_PATTERN.test(char)) {
            result.push(char);
            return;
        }

        // 그 외 문자
        result.push(char);
    });

    return result.join('');
}