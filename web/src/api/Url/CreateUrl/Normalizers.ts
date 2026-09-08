import type { CreateUrlPayloadInterface, CreateUrlRequestBodyInterface } from '@/api/Url/CreateUrl/Types';

export const normalizeCreateUrlPayload = (payload: CreateUrlPayloadInterface): CreateUrlRequestBodyInterface => ({
  target_url: payload.targetUrl,
});
