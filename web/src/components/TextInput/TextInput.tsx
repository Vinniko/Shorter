import { useState } from 'react';
import TextField from '@mui/material/TextField';

import styles from './TextInput.module.scss';

interface Props {
  label: string;
  value: string;
  onChange: (value: string) => void;
  placeholder?: string;
  error?: boolean;
  helperText?: string;
}

function TextInput({ label, value, onChange, placeholder, error, helperText }: Props) {
  const [isFocused, setIsFocused] = useState(false);
  const showLabel = !isFocused && !value;

  return (
    <TextField
      className={styles.text_input}
      label={showLabel ? label : undefined}
      placeholder={placeholder}
      value={value}
      error={error}
      helperText={helperText}
      onChange={(event) => onChange(event.target.value)}
      onFocus={() => setIsFocused(true)}
      onBlur={() => setIsFocused(false)}
      sx={{
        backgroundColor: '#fff',
        borderRadius: 2,
        '& .MuiOutlinedInput-notchedOutline': {
          border: 'none',
        },
      }}
      fullWidth
    />
  );
}

export default TextInput;
