import type { UrlInterface, UrlViewObjectInterface } from '@/api/Url/Types';

export interface LinkStatsViewObjectInterface {
  url: UrlViewObjectInterface;
  click_qty: number;
}

export interface LinkStatsInterface {
  url: UrlInterface;
  clickQty: number;
}
