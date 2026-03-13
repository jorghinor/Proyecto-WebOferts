const DEFAULT_IMAGE = "/assets/img/logo.png";

function normalizeImagePath(image) {
  if (!image) return "";
  if (image.startsWith("http://") || image.startsWith("https://")) return image;
  return image.startsWith("/") ? image : `/${image}`;
}

export function resolveImagePath(image) {
  const normalized = normalizeImagePath(image);
  if (!normalized) return window.location.origin + DEFAULT_IMAGE;
  if (normalized.startsWith("http://") || normalized.startsWith("https://")) return normalized;
  return window.location.origin + normalized;
}

function isTrustedUpload(image) {
  return normalizeImagePath(image).includes("/storage/images/");
}

function isDemoImage(image) {
  const normalized = normalizeImagePath(image).toLowerCase();
  if (!normalized) return true;

  return [
    "/assets/img/product/img",
    "/assets/img/featured/img",
    "/assets/img/category/img",
    "placeimg",
    "lorempixel",
    "loremflickr",
    "dummyimage",
    "via.placeholder",
    "picsum",
    "animals",
  ].some((token) => normalized.includes(token));
}

function getNegocioLogo(anuncio) {
  return anuncio && anuncio.negocio && anuncio.negocio.logo ? anuncio.negocio.logo : "";
}

export function getAnuncioCardImage(anuncio) {
  const fotos = Array.isArray(anuncio && anuncio.fotos) ? anuncio.fotos : [];
  const preferred = fotos.find((foto) => foto && foto.url && isTrustedUpload(foto.url));
  if (preferred) return resolveImagePath(preferred.url);

  const alternative = fotos.find((foto) => foto && foto.url && !isDemoImage(foto.url));
  if (alternative) return resolveImagePath(alternative.url);

  const negocioLogo = getNegocioLogo(anuncio);
  if (negocioLogo && !isDemoImage(negocioLogo)) {
    return resolveImagePath(negocioLogo);
  }

  return resolveImagePath(DEFAULT_IMAGE);
}
