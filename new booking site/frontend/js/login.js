// Simple hardcoded login credentials (for demo)
const validUsername = 'bravin';
const validPassword = 'password123';

const loginForm = document.getElementById('loginForm');

loginForm.addEventListener('submit', (e) => {
  e.preventDefault();

  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value.trim();

  if (username === validUsername && password === validPassword) {
    alert('Login successful! Redirecting to booking page...');
    window.location.href = 'index.html';  // redirect to booking page
  } else {
    alert('Invalid username or password. Please try again.');
    loginForm.reset();
  }
  <script>
  document.getElementById('registerForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append("name", document.getElementById("name").value);
    formData.append("email", document.getElementById("email").value);
    formData.append("password", document.getElementById("password").value);
    formData.append("user_type", document.getElementById("user_type").value);

    fetch("http://localhost/bnb-backend/public/register.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
      document.getElementById('registerModal').style.display = 'none';
    })
    .catch(err => alert("Registration failed"));
  });
</script>

});
