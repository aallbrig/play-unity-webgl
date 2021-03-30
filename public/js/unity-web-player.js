function calculateVerticalHeightCSSVar() {
    return window.innerHeight * 0.01;
}

document.documentElement.style.setProperty('--vh', `${calculateVerticalHeightCSSVar()}px`);
// TODO: Debounce resize event
window.addEventListener('resize', () => document.documentElement.style.setProperty('--vh', `${calculateVerticalHeightCSSVar()}px`));
