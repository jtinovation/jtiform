import SignaturePad from 'signature_pad';

try {
  window.SignaturePad = SignaturePad;
} catch (e) {
  console.error('Failed to initialize SignaturePad:', e);
}
