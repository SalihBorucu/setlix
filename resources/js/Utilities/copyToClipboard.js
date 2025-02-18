export default (text) => {
    // /* Select the text field */
    // text.select();
    // text.setSelectionRange(0, 99999); /* For mobile devices */

    /* Copy the text inside the text field */
    navigator.clipboard.writeText(text);
}