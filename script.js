// script.js - signup modal and simple localStorage signup
(function(){
  const openBtn = document.querySelector('.create-btn');
  const modal = document.getElementById('signupModal');
  const backdrop = document.getElementById('modalBackdrop');
  const closeBtn = document.getElementById('closeModal');
  const cancelBtn = document.getElementById('cancelBtn');
  const form = document.getElementById('signupForm');
  const message = document.getElementById('signupMessage');

  function showModal(){
    modal.setAttribute('aria-hidden','false');
    message.textContent = '';
    form.reset();
    setTimeout(()=>{
      document.getElementById('fullName').focus();
    },50);
  }
  function hideModal(){
    modal.setAttribute('aria-hidden','true');
  }

  openBtn && openBtn.addEventListener('click', showModal);
  closeBtn && closeBtn.addEventListener('click', hideModal);
  cancelBtn && cancelBtn.addEventListener('click', hideModal);
  backdrop && backdrop.addEventListener('click', hideModal);

  form && form.addEventListener('submit', function(e){
    e.preventDefault();
    const fullName = form.fullName.value.trim();
    const email = form.email.value.trim().toLowerCase();
    const password = form.password.value;
    const confirm = form.confirm.value;

    if(!fullName || !email || !password){
      message.style.color = 'crimson';
      message.textContent = 'Please fill out all fields.';
      return;
    }
    if(password.length < 6){
      message.style.color = 'crimson';
      message.textContent = 'Password must be at least 6 characters.';
      return;
    }
    if(password !== confirm){
      message.style.color = 'crimson';
      message.textContent = 'Passwords do not match.';
      return;
    }

    // Send to server (register.php)
    message.style.color = 'black';
    message.textContent = 'Creating account...';

    fetch('register.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name: fullName, email: email, password: password })
    }).then(r => r.json())
      .then(data => {
        if(data && data.success){
          message.style.color = 'green';
          message.textContent = data.message || 'Account created successfully.';
          setTimeout(hideModal, 1000);
        } else {
          message.style.color = 'crimson';
          message.textContent = data.message || 'Signup failed';
        }
      }).catch(err => {
        message.style.color = 'crimson';
        message.textContent = 'Network or server error';
        console.error(err);
      });
  });
})();
