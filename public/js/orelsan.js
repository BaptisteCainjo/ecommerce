const liProfile = document.querySelector('.li-profile');

liProfile.addEventListener('click', function () {
const txtProfile = document.querySelector('.txt-profile');
const btnLogout = document.querySelector('.btnLogout');
const hr = document.querySelector('hr');
const imgProfile = document.querySelector('.img-profile');
const pAfter = document.querySelector('.img-profile p::after');


txtProfile.classList.toggle('open');
btnLogout.classList.toggle('open');
hr.classList.toggle('open');
imgProfile.classList.toggle('open');
pAfter.style.borderTop = '0 solid #666666'
pAfter.style.borderBottom = '7px solid #666666'
});