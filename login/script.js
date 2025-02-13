const wrapper = document.querySelector('.wrapper');
const signUpLink = document.querySelector('.signUp-link');
const signInLink = document.querySelector('.signIn-link');
const passwordInput = document.getElementById('password');
const passwordError = document.getElementById('password-error');

signUpLink.addEventListener('click', () => {
  wrapper.classList.add('animate-signIn');
  wrapper.classList.remove('animate-signUp');
});

signInLink.addEventListener('click', () => {
  wrapper.classList.add('animate-signUp');
  wrapper.classList.remove('animate-signIn');
});

passwordInput.addEventListener('input', () => {
  const password = passwordInput.value;
  const isValid = validatePassword(password);
  if (!isValid) {
    passwordError.style.display = 'block';
    passwordError.textContent = 'Weak Password';
  } else {
    passwordError.style.display = 'none';
  }
});

function validatePassword(password) {
  const minLength = 8;
  const hasNumber = /\d/;
  const hasUpperCase = /[A-Z]/;
  const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/;

  return (
    password.length >= minLength &&
    hasNumber.test(password) &&
    hasUpperCase.test(password) &&
    hasSpecialChar.test(password)
  );
}




passwordInput.addEventListener('focus', () => {
  passwordInput.setAttribute('placeholder', 'Eg: Nandhu@123');
});

passwordInput.addEventListener('blur', () => {
  passwordInput.removeAttribute('placeholder');
});
