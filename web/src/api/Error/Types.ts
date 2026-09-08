export interface ValidationErrorViewObjectInterface {
  message: string;
  violations: Record<string, string[]>;
}

export interface ValidationErrorInterface {
  message: string;
  violations: Record<string, string[]>;
}
