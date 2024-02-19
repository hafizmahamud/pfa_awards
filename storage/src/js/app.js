import { Tooltip, Toast, Popover } from 'bootstrap';

[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function (tooltipTriggerEl) {
    return new Tooltip(tooltipTriggerEl);
});

[].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]')).map(function (popoverTriggerEl) {
    return new Popover(popoverTriggerEl);
});

[].slice.call(document.querySelectorAll('.toast')).map(function (toastEl) {
  return new Toast(toastEl);
});
