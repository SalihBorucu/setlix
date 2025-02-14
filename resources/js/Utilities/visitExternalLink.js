export default (url, inNewTab = false, isDownloadable = false) => {
  const a = document.createElement("a");
  a.style.display = "none";

  if (inNewTab) {
    a.target = "_blank";
  }

  if (isDownloadable) {
    // a.download needed for supporting download csv files on safari
    let urlParts = url.split('/');
    let count = urlParts.length;

    a.download = urlParts[count - 1]
  }

  document.body.appendChild(a);

  a.href = url;

  a.click();

  window.URL.revokeObjectURL(a.href);
  document.body.removeChild(a);
}
