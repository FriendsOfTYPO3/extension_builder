export function resizeLeftSidebar(isRightColumnFullWidth) {
    if (isRightColumnFullWidth) {
        document.getElementById('left-column').style.opacity = '0';
    } else {
        document.getElementById('left-column').style.opacity = '1';
    }
}
