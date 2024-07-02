export function resizeSettingsSidebar(isRightColumnFullWidth) {
    if (isRightColumnFullWidth) {
        document.getElementById('settings-column').style.opacity = '0';
    } else {
        document.getElementById('settings-column').style.opacity = '1';
    }
}
