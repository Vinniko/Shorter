import { denormalizeUrl } from '@/api/Url/Denormalizers';

import type { LinkStatsInterface, LinkStatsViewObjectInterface } from '@/api/Url/GetLinkStats/Types';

export const denormalizeLinkStats = (viewObject: LinkStatsViewObjectInterface): LinkStatsInterface => ({
  url: denormalizeUrl(viewObject.url),
  clickQty: viewObject.click_qty,
});
