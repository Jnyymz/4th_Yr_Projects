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
<title>Skills | My Portfolio</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Tailwind -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body {
  font-family: 'Poppins', sans-serif;
  min-height: 100vh;
  background:
    linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
    url("https://i.pinimg.com/736x/e1/89/8d/e1898d3c2d18042aad07e8e7f154ac9c.jpg");
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  color: #fff;
}

.glass {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(12px);
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 8px 25px rgba(0,0,0,0.25);
}

.nav-link {
  color: #fff;
  transition: all 0.3s ease;
  font-weight: 400;
}
.nav-link:hover,
.nav-link.active {
  transform: scale(1.15);
  font-weight: 600;
  color: #f1f5f9;
}

.footer-icon {
  color: #fff;
  font-size: 1.4rem;
  transition: transform 0.3s ease;
}
.footer-icon:hover { transform: scale(1.2); }

.add-skill {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  font-size: 2rem;
  cursor: pointer;
  transition: all 0.3s ease;
}
.add-skill:hover { transform: scale(1.2); }

.skill-animate { opacity: 0; transform: translateY(12px); transition: transform .6s cubic-bezier(.2,.9,.2,1), opacity .6s ease; }
.skill-animate.show { opacity: 1; transform: translateY(0); }

/* Skeleton placeholder */
.skeleton { background: linear-gradient(90deg,#2b2b2b 25%, #3a3a3a 50%, #2b2b2b 75%); background-size: 200% 100%; animation: shimmer 1.2s linear infinite; border-radius:8px; }
.skeleton-title{ height:18px; width:60%; margin-bottom:8px; }
.skeleton-desc{ height:12px; width:100%; margin-bottom:6px; }
@keyframes shimmer{ 0%{background-position:200% 0} 100%{background-position:-200% 0} }

@keyframes fadeInUp {
      0% {
          opacity: 0;
          transform: translateY(20px);
      }
      100% {
          opacity: 1;
          transform: translateY(0);
      }
    }


.glass-box {
  background: rgba(255, 255, 255, 0.18);
  backdrop-filter: blur(12px);
  border-radius: 24px;
  padding: 3rem;
  box-shadow: 0 10px 30px rgba(0,0,0,0.25);
  animation: fadeInUp 1s ease forwards;
  opacity: 0;
}

.skill-buttons { margin-top: 0.5rem; }
.skill-buttons button { margin-right: 0.5rem; }
</style>
</head>
<body>

<div class="container-fluid px-4 px-md-5 py-4">

  <!-- NAVBAR -->
  <nav class="glass max-w-6xl mx-auto mb-4">
    <ul class="nav justify-content-center gap-4">
      <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link active" href="skill.php">Skill</a></li>
      <li class="nav-item"><a class="nav-link" href="projects.php">Projects</a></li>
      <li class="nav-item"><a class="nav-link" href="education.php">Education</a></li>
    </ul>
  </nav>

  <!-- HEADING BOX -->
  <div class="glass max-w-6xl mx-auto text-center mb-5 heading-box" style="cursor:pointer;">
    <h1 class="fw-bold text-center mb-4 heading-text" style="font-size: 2rem;">Technical Skills</h1>
    <p class="opacity-90 subheading-text">
      <i>A summary of my current technical skills and the technologies I actively use and study.</i>
    </p>
  </div>

  <!-- SKILLS GRID -->
  <div class="max-w-6xl mx-auto ">
    <div class="row g-4 skill-grid">
      <!-- Skills and plus sign card will be dynamically loaded here -->
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="glass max-w-6xl mx-auto text-center mt-5 skill-animate">
    <div class="d-flex justify-content-center gap-4 mb-2">
      <a href="#" class="footer-icon"><i class="bi bi-github"></i></a>
      <a href="#" class="footer-icon"><i class="bi bi-envelope"></i></a>
      <a href="#" class="footer-icon"><i class="bi bi-telephone"></i></a>
    </div>
    <div class="opacity-80">© 2025 My Portfolio
      <div class="mt-2">
        <a href="logout.php" class="text-white" style="text-decoration:underline;">Logout</a>
      </div>
    </div>
  </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const skillGrid = document.querySelector('.skill-grid');
const apiURL = 'api.php'; // use main API which contains skills endpoints

// Load skills
function loadSkills(){
  renderSkeleton(4);
  const params = new URLSearchParams();
  params.append('action','get_skills');
  fetch(apiURL, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params.toString()})
  .then(res=>res.json())
  .then(data=>{
    skillGrid.innerHTML='';
    data.data.forEach(skill=>addSkillCard(skill));
    addPlusCard();
    // staggered fade-in
    animateSkillCards();
  })
  .catch(err=>{
    console.error('Failed to load skills', err);
  });
}

function renderSkeleton(count=4){
  skillGrid.innerHTML='';
  for(let i=0;i<count;i++){
    const col=document.createElement('div'); col.className='col-md-6';
    col.innerHTML = `<div class="glass h-100 skill-animate p-3">
                      <div class="skeleton skeleton-title" style="margin-bottom:12px;"></div>
                      <div class="skeleton skeleton-desc"></div>
                      <div class="skeleton skeleton-desc" style="width:90%;"></div>
                    </div>`;
    skillGrid.appendChild(col);
  }
}

function animateSkillCards(){
  const items = document.querySelectorAll('.skill-animate');
  items.forEach((el,i)=>{
    if (!el.classList.contains('show')){
      setTimeout(()=> el.classList.add('show'), i * 120);
    }
  });
}

// Add skill card
function addSkillCard(skill){
  const col=document.createElement('div'); col.className='col-md-6';
  col.innerHTML=`<div class="glass h-100 skill-animate" data-id="${skill.id}" style="cursor:pointer;">
                    <h5 class="fw-semibold mb-2">${skill.title}</h5>
                    <p class="opacity-90">${skill.description}</p>
                  </div>`;
  const glass = col.querySelector('.glass');

  glass.ondblclick=()=>{
    Swal.fire({
      title:'Skill Actions',
      showDenyButton:true,
      showCancelButton:true,
      confirmButtonText:'Edit',
      denyButtonText:'Delete',
      backdrop:false
    }).then(result=>{
      if(result.isConfirmed){ // Edit
        Swal.fire({
          title:'Edit Skill',
          html:`<input id="t" class="swal2-input" value="${skill.title}"><textarea id="d" class="swal2-textarea">${skill.description}</textarea>`,
          showCancelButton:true,
          backdrop:false,
          preConfirm:()=>({t:document.getElementById('t').value,d:document.getElementById('d').value})
        }).then(r=>{
          if(r.isConfirmed){
            const up = new URLSearchParams();
            up.append('action','update_skill');
            up.append('id', skill.id);
            up.append('title', r.value.t);
            up.append('description', r.value.d);
            fetch(apiURL,{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: up.toString()})
            .then(()=>loadSkills());
          }
        });
      }
      if(result.isDenied){ // Delete
        const del = new URLSearchParams();
        del.append('action','delete_skill');
        del.append('id', skill.id);
        fetch(apiURL,{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: del.toString()})
        .then(()=>loadSkills());
      }
    });
  };
  skillGrid.appendChild(col);
  // ensure this card animates in (adds the 'show' class shortly after insertion)
  setTimeout(()=>{
    const elm = col.querySelector('.skill-animate');
    if(elm) elm.classList.add('show');
  }, 40);
}

// Plus card to add new skill
function addPlusCard(){
  const col=document.createElement('div'); col.className='col-md-6';
  col.innerHTML=`<div class="glass h-100 add-skill skill-animate d-flex justify-content-center align-items-center" style="font-size:2rem;cursor:pointer;">
                  <i class="bi bi-plus"></i>
                 </div>`;
  col.querySelector('.add-skill').onclick=()=>{
    Swal.fire({
      title:'Add Skill',
      html:`<input id="t" class="swal2-input" placeholder="Title"><textarea id="d" class="swal2-textarea" placeholder="Description"></textarea>`,
      showCancelButton:true,
      backdrop:false,
      preConfirm:()=>({t:document.getElementById('t').value,d:document.getElementById('d').value})
    }).then(r=>{
      if(r.isConfirmed){
        const add = new URLSearchParams();
        add.append('action','add_skill');
        add.append('title', r.value.t);
        add.append('description', r.value.d);
        fetch(apiURL,{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: add.toString()})
        .then(()=>loadSkills());
      }
    });
  };
  skillGrid.appendChild(col);
  // ensure plus card also animates
  setTimeout(()=> col.querySelector('.skill-animate').classList.add('show'), 40);
}

// Heading box editable (frontend only)
const headingBox = document.querySelector('.heading-box');
headingBox.addEventListener('dblclick', ()=>{
  Swal.fire({
    title:'Edit Heading & Description',
    html:`<input id="swal-heading" class="swal2-input" value="${document.querySelector('.heading-text').innerText}">
          <textarea id="swal-subheading" class="swal2-textarea">${document.querySelector('.subheading-text').innerText}</textarea>`,
    showCancelButton:true,
    backdrop:false
  }).then(r=>{
    if(r.isConfirmed){
      document.querySelector('.heading-text').innerText=document.getElementById('swal-heading').value;
      document.querySelector('.subheading-text').innerText=document.getElementById('swal-subheading').value;
    }
  });
});

loadSkills();
</script>

</body>
</html>
