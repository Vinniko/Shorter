import type { UrlInterface, UrlViewObjectInterface } from '@/api/Url/Types';

export const denormalizeUrl = (viewObject: UrlViewObjectInterface): UrlInterface => ({
  id: viewObject.id,
  code: viewObject.code,
  targetUrl: viewObject.target_url,
  shortUrl: viewObject.short_url,
});
