/**
 * Laravel API bilan aloqa.
 *
 * Bazaviy manzil `VITE_API_URL` dan olinadi. Ishlab chiqarishda bo'sh qoldiriladi —
 * u holda `/api` shu domenning o'zidan so'raladi (nginx uni backend/public ga uzatadi).
 */
const BASE = (import.meta.env.VITE_API_URL ?? '').replace(/\/$/, '');

export const apiUrl = (path: string) => `${BASE}/api${path}`;

async function request<T>(path: string, init?: RequestInit): Promise<T> {
  const response = await fetch(apiUrl(path), {
    headers: { Accept: 'application/json', ...(init?.headers ?? {}) },
    ...init,
  });

  if (!response.ok) {
    const error = new Error(`API ${response.status}: ${path}`) as Error & {
      status: number;
      body?: unknown;
    };
    error.status = response.status;
    error.body = await response.json().catch(() => undefined);
    throw error;
  }

  return response.json() as Promise<T>;
}

export const getContent = () => request<Record<string, unknown>>('/content');

export interface ContactPayload {
  name: string;
  email: string;
  subject: string;
  message: string;
}

export interface ServiceOrderPayload {
  name: string;
  email: string;
  phone: string;
  message: string;
  serviceId: string | number;
  serviceName: string;
  servicePrice: string;
}

const post = <T>(path: string, payload: unknown) =>
  request<T>(path, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  });

type FormResult = { success: boolean; message: string };

export const sendContact = (payload: ContactPayload) =>
  post<FormResult>('/contact', payload);

export const sendServiceOrder = (payload: ServiceOrderPayload) =>
  post<FormResult>('/service-order', {
    ...payload,
    // bo'sh string emas, null yuboramiz — server `nullable|integer` kutadi
    serviceId: payload.serviceId === '' ? null : Number(payload.serviceId),
  });
