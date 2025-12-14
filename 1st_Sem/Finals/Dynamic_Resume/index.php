<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>My Portfolio</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body {
  font-family: 'Poppins', sans-serif;
  min-height: 100vh;
  background:
    linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
    url("https://i.pinimg.com/736x/e1/89/8d/e1898d3c2d18042aad07e8e7f154ac9c.jpg");
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
}

.glass-box {
  background: rgba(255,255,255,0.18);
  backdrop-filter: blur(12px);
  border-radius: 24px;
  padding: 3rem;
  box-shadow: 0 10px 30px rgba(0,0,0,0.25);
  animation: fadeInUp 1s ease forwards;
  opacity: 0;
}

.custom-btn { border:1px solid #fff; color:#fff;}
.custom-btn:hover { background:#fff; color:#111; }

.save-btn {
  border:1px solid #CC3B8D; /* orange-ish */
  background: #8C2066;
  color:#fff;
  transition: all 0.3s ease;
}
.save-btn:hover { background:#CC3B8D; color:#111; }

.icon-link { color:#fff; font-size:1.5rem; cursor:pointer; }

#profileImageBox img { max-height:450px; width:100%; object-fit:cover; border-radius:.75rem; }

@keyframes fadeInUp { from{opacity:0;transform:translateY(20px);} to{opacity:1;transform:translateY(0);} }
</style>
</head>
<body>

<div class="container-fluid px-4 px-md-5 py-5">
<div class="glass-box max-w-6xl mx-auto text-white">

<!-- NAV -->
<nav class="d-flex justify-content-between align-items-center mb-5">
  <h4 class="fw-semibold">My Portfolio</h4>
  <div class="d-flex gap-4">
    <span class="icon-link editable-contact" data-field="email_link"><i class="bi bi-envelope"></i></span>
    <span class="icon-link editable-contact" data-field="github_link"><i class="bi bi-github"></i></span>
    <span class="icon-link editable-contact" data-field="phone_number"><i class="bi bi-telephone"></i></span>
  </div>
</nav>

<!-- HERO -->
<div class="row align-items-center g-5 mb-5">
<div class="col-lg-5 text-center">
  <div id="profileImageBox"
       class="d-flex align-items-center justify-content-center"
       style="height:450px; border:2px dashed rgba(255,255,255,.4); cursor:pointer;">
    <i class="bi bi-plus-lg fs-1"></i>
  </div>
</div>

<div class="col-lg-7">
  <h1 class="fw-bold mb-3 display-5 editable-text" data-field="heading">Hi! I'm User</h1>
  <h5 class="fw-light mb-4 editable-text" data-field="subheading">Your profession</h5>
  <p class="opacity-90 editable-text" data-field="about_text">Double click to edit your description.</p>

  <div class="mt-4 d-flex flex-wrap gap-3 align-items-center">
    <a href="skill.php" class="btn custom-btn px-4 rounded-pill">Skills</a>
    <a href="projects.php" class="btn custom-btn px-4 rounded-pill">Projects</a>
    <a href="education.php" class="btn custom-btn px-4 rounded-pill">Education</a>
    <button id="saveProfileBtn" class="btn save-btn px-4 rounded-pill">Save</button>
  </div>
</div>
</div>

<!-- INFO ROW -->
<div class="row text-center g-4" id="infoRow">
  <div class="col-md-4">
    <div id="addSectionBtn" class="p-4 border border-dashed rounded-3" style="cursor:pointer;">
      <i class="bi bi-plus-lg fs-3"></i>
    </div>
  </div>
</div>

<!-- FOOTER -->
<div class="text-center mt-5 opacity-75">© 2025 My Portfolio
  <div class="mt-2">
    <a href="logout.php" class="text-white" style="text-decoration:underline;">Logout</a>
  </div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let profileData = {};

// Load profile
function loadProfile(){
  fetch('api.php',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'action=get_profile'
  })
  .then(res=>res.json())
  .then(data=>{
    if(data.profile){
      profileData.heading = data.profile.heading || '';
      profileData.subheading = data.profile.subheading || '';
      profileData.about_text = data.profile.about_text || '';
      profileData.github_link = data.profile.github_link || '';
      profileData.email_link = data.profile.email_link || '';
      profileData.phone_number = data.profile.phone_number || '';
      document.querySelector('[data-field="heading"]').innerText = profileData.heading;
      document.querySelector('[data-field="subheading"]').innerText = profileData.subheading;
      document.querySelector('[data-field="about_text"]').innerText = profileData.about_text;
      if(data.profile.image_path){
        profileData.image_path = data.profile.image_path;
        profileImageBox.innerHTML = `<img src="${profileData.image_path}">`;
      }
    }
    data.sections.forEach(addSectionCard);
  });
}
loadProfile();

// Edit text
document.querySelectorAll('.editable-text').forEach(el=>{
  el.ondblclick = ()=>{
    Swal.fire({
      title:'Edit content',
      input:'textarea',
      inputValue:el.innerText,
      showCancelButton:true,
      backdrop:false
    }).then(r=>{
      if(r.isConfirmed){
        el.innerText=r.value;
        profileData[el.dataset.field] = r.value;
      }
    });
  }
});

// Contact icons
document.querySelectorAll('.editable-contact').forEach(icon=>{
  icon.ondblclick = ()=>{
    Swal.fire({ title:'Update contact', input:'text', showCancelButton:true, backdrop:false })
    .then(r=>{
      if(r.isConfirmed){
        profileData[icon.dataset.field] = r.value;
      }
    });
  }
});

// Image update
profileImageBox.ondblclick = ()=>{
  Swal.fire({
    title:'Image URL',
    input:'text',
    showCancelButton:true,
    backdrop:false
  }).then(r=>{
    if(r.isConfirmed){
      profileData.image_path = r.value;
      profileImageBox.innerHTML = `<img src="${r.value}">`;
    }
  });
};

// Save button
document.getElementById('saveProfileBtn').onclick = ()=>{
  const params = new URLSearchParams();
  params.append('action','save_profile');
  params.append('heading', profileData.heading || '');
  params.append('subheading', profileData.subheading || '');
  params.append('about_text', profileData.about_text || '');
  params.append('github_link', profileData.github_link || '');
  params.append('email_link', profileData.email_link || '');
  params.append('phone_number', profileData.phone_number || '');
  params.append('image_path', profileData.image_path || '');

  const btn = document.getElementById('saveProfileBtn');
  btn.disabled = true;
  const originalText = btn.innerText;
  btn.innerText = 'Saving...';

  fetch('api.php',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: params.toString()
  })
  .then(res=>res.json())
  .then(res=>{
    if(res.status==='success'){
      Swal.fire('Saved!','Your profile has been saved.','success');
    } else {
      console.error('Save error:', res);
      Swal.fire('Error', res.message || 'Failed to save profile','error');
    }
  })
  .catch(err=>{
    console.error('Network error:', err);
    Swal.fire('Error','Network error while saving profile','error');
  })
  .finally(()=>{
    btn.disabled = false;
    btn.innerText = originalText;
  });
};

// Info sections
function addSectionCard(sec){
  const div=document.createElement('div');
  div.className='col-md-4';
  div.innerHTML=`<div class="p-4" data-id="${sec.id}"><h6>${sec.title}</h6><p class="opacity-80">${sec.description}</p></div>`;
  div.ondblclick=()=>{
    Swal.fire({
      title:'Edit section',
      showDenyButton:true,
      confirmButtonText:'Edit',
      denyButtonText:'Delete',
      backdrop:false
    }).then(r=>{
      if(r.isConfirmed){
        Swal.fire({ title:'Edit section', html:`<input id="t" class="swal2-input" value="${sec.title}"><textarea id="d" class="swal2-textarea">${sec.description}</textarea>`, showCancelButton:true, backdrop:false, preConfirm:()=>({t:document.getElementById('t').value,d:document.getElementById('d').value}) })
        .then(x=>{
          if(x.isConfirmed){
            fetch('api.php',{ method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=update_section&id=${sec.id}&title=${x.value.t}&description=${x.value.d}` }).then(()=>location.reload());
          }
        });
      }
      if(r.isDenied){
        fetch('api.php',{ method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=delete_section&id=${sec.id}` }).then(()=>location.reload());
      }
    });
  };
  infoRow.prepend(div);
}

// Add section
addSectionBtn.onclick=()=>{
  Swal.fire({ title:'New section', html:`<input id="t" class="swal2-input" placeholder="Title"><textarea id="d" class="swal2-textarea" placeholder="Description"></textarea>`, showCancelButton:true, backdrop:false, preConfirm:()=>({t:document.getElementById('t').value,d:document.getElementById('d').value}) })
  .then(r=>{
    if(r.isConfirmed){
      fetch('api.php',{ method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=add_section&title=${r.value.t}&description=${r.value.d}` }).then(()=>location.reload());
    }
  });
};
</script>

</body>
</html>
