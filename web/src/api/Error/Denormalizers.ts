import type { FetchBaseQueryError } from '@reduxjs/toolkit/query';
import type { SerializedError } from '@reduxjs/toolkit';

import type { ValidationErrorInterface, ValidationErrorViewObjectInterface } from '@/api/Error/Types';

const toCamelCase = (field: string): string =>
  field.replace(/_([a-z0-9])/g, (_match, letter: string) => letter.toUpperCase());

const isValidationErrorViewObject = (data: unknown): data is ValidationErrorViewObjectInterface =>
  typeof data === 'object' && data !== null && 'violations' in data && 'message' in data;

export const denormalizeValidationError = (
  error: FetchBaseQueryError | SerializedError | undefined,
): ValidationErrorInterface | null => {
  if (!error || !('data' in error) || !isValidationErrorViewObject(error.data)) {
    return null;
  }

  const { message, violations } = error.data;

  return {
    message,
    violations: Object.fromEntries(
      Object.entries(violations).map(([field, messages]) => [toCamelCase(field), messages]),
    ),
  };
};
