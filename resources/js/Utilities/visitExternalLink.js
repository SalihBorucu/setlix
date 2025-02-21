export default (url, inNewTab = false, isDownloadable = false) => {
  if (inNewTab) {
    // Open in new tab directly using window.open
    window.open(url, '_blank');
    return;
  }

  const a = document.createElement("a");
  a.style.display = "none";

  if (isDownloadable) {
    // a.download needed for supporting download csv files on safari
    let urlParts = url.split('/');
    let count = urlParts.length;
    a.download = urlParts[count - 1];
  }

  document.body.appendChild(a);
  a.href = url;
  a.click();
  window.URL.revokeObjectURL(a.href);
  document.body.removeChild(a);
}
