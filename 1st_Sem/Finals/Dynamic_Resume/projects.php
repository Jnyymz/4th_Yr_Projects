<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <title>Projects | My Portfolio</title>

  <!-- Bootstrap -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  >

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Bootstrap Icons -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" 
    rel="stylesheet"
  >

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link 
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" 
    rel="stylesheet"
  >

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

    .project-link {
      position: absolute;
      bottom: 20px;
      right: 24px;
      color: #fff;
      font-size: 1.4rem;
      transition: transform 0.3s ease;
    }

    .project-link:hover {
      transform: scale(1.2);
    }

    .footer-icon {
      color: #fff;
      font-size: 1.4rem;
      transition: transform 0.3s ease;
    }
    .footer-icon:hover { transform: scale(1.2); }

    /* animation for project cards */
    .project-animate { opacity: 0; transform: translateY(12px); transition: transform .6s cubic-bezier(.2,.9,.2,1), opacity .6s ease; }
    .project-animate.show { opacity: 1; transform: translateY(0); }

  </style>
</head>

<body>

<div class="container-fluid px-4 px-md-5 py-4">

  <!-- NAVBAR -->
  <nav class="glass max-w-6xl mx-auto mb-4">
    <ul class="nav justify-content-center gap-4">
      <li class="nav-item">
        <a class="nav-link" href="index.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="skill.php">Skill</a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="projects.php">Projects</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="education.php">Education</a>
      </li>
    </ul>
  </nav>

  <!-- HEADING -->
  <div class="glass max-w-6xl mx-auto text-center mb-5">
    <h1 class="fw-bold text-center mb-4" style="font-size: 2rem;">Projects</h1>
    <p class="opacity-90">
      <i>A collection of projects showcasing my experience in development and design.</i>
    </p>
  </div>

  <!-- PROJECT LIST -->
  <div id="projectList" class="max-w-6xl mx-auto d-grid gap-4">
    <!-- Existing static projects will be initialized by JS for consistent behavior -->
  </div>

  <!-- FOOTER -->
  <footer class="glass max-w-6xl mx-auto text-center mt-5">
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

document.addEventListener("DOMContentLoaded", () => {
    const projectList = document.getElementById('projectList');

    // Helper to create a project card element
    function createProjectCard({id=null, title='', description='', github=''}){
      const card = document.createElement('div');
        card.className = 'glass position-relative project-card project-animate';
      if(id) card.dataset.id = id;
      card.innerHTML = `
        <h5 class="fw-semibold mb-2">${escapeHtml(title)}</h5>
        <p class="opacity-90 mb-5">${escapeHtml(description)}</p>
        <a class="project-link" ${github ? `href="${escapeAttr(github)}" target="_blank"` : 'href="#" onclick="event.preventDefault();"'}>
          <i class="bi bi-github"></i>
        </a>
      `;

      // dblclick: Edit / Delete actions
      card.ondblclick = ()=>{
        Swal.fire({
          title: 'Project Actions',
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: 'Edit',
          denyButtonText: 'Delete',
          backdrop: false
        }).then(r => {
          if(r.isConfirmed){
            // Edit
            Swal.fire({
              title: 'Edit Project',
              html: `<input id="t" class="swal2-input" placeholder="Title" value="${escapeAttr(title)}">`+
                    `<textarea id="d" class="swal2-textarea" placeholder="Description">${escapeHtml(description)}</textarea>`+
                    `<input id="g" class="swal2-input" placeholder="GitHub link" value="${escapeAttr(github)}">`,
              showCancelButton:true,
              backdrop:false,
              preConfirm: ()=>({ t: document.getElementById('t').value, d: document.getElementById('d').value, g: document.getElementById('g').value })
            }).then(res => {
              if(res.isConfirmed){
                // update DOM
                card.querySelector('h5').innerText = res.value.t;
                card.querySelector('p').innerText = res.value.d;
                const link = card.querySelector('.project-link');
                if(res.value.g && res.value.g.trim()!==''){
                  link.setAttribute('href', res.value.g);
                  link.setAttribute('target','_blank');
                } else {
                  link.removeAttribute('href');
                  link.setAttribute('href','#');
                  link.setAttribute('onclick','event.preventDefault();');
                }
                // optional: send update to backend if id exists
                if(card.dataset.id){
                  const params = new URLSearchParams(); params.append('action','update_project'); params.append('id', card.dataset.id); params.append('title', res.value.t); params.append('description', res.value.d);
                    params.append('github', res.value.g || '');
                  fetch('api.php',{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params.toString()});
                }
              }
            });
          }
          if(r.isDenied){
            Swal.fire({ title: 'Delete project?', showCancelButton:true, backdrop:false }).then(x=>{
              if(x.isConfirmed){
                // remove DOM
                card.remove();
                // optional backend call
                if(card.dataset.id){ const p = new URLSearchParams(); p.append('action','delete_project'); p.append('id', card.dataset.id); fetch('api.php',{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: p.toString()}); }
              }
            });
          }
        });
      };

      return card;
    }

    // Helpers to escape
    function escapeHtml(s){ if(!s) return ''; return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    function escapeAttr(s){ if(!s) return ''; return s.replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

    // No initial projects — users should create their own entries.

    // Add plus-card
    const plusCol = document.createElement('div'); plusCol.className='glass position-relative add-project project-animate';
    plusCol.style.cursor='pointer';
    plusCol.innerHTML = `<div style="display:flex;align-items:center;justify-content:center;height:120px;font-size:2rem;"> <i class="bi bi-plus-lg"></i> </div>`;
    plusCol.onclick = ()=>{
      Swal.fire({
        title:'Add Project',
        html:`<input id="t" class="swal2-input" placeholder="Title">`+
             `<textarea id="d" class="swal2-textarea" placeholder="Description"></textarea>`+
             `<input id="g" class="swal2-input" placeholder="GitHub link (optional)">`,
        showCancelButton:true,
        backdrop:false,
        preConfirm: ()=>({t:document.getElementById('t').value, d:document.getElementById('d').value, g:document.getElementById('g').value})
      }).then(r=>{
        if(r.isConfirmed){
          const obj = { title: r.value.t, description: r.value.d, github: r.value.g };
          const card = createProjectCard(obj);
          // insert new project above the plus card
          projectList.insertBefore(card, plusCol);
          // animate in (slightly slower, small delay)
          setTimeout(()=> card.classList.add('show'), 40);
          // optional: persist to backend
          const params = new URLSearchParams(); params.append('action','add_project'); params.append('title', obj.title); params.append('description', obj.description); params.append('github', obj.github || '');
          fetch('api.php',{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params.toString()})
          .then(res=>res.json()).then(resp=>{
            if(resp && resp.status==='success' && resp.id){ card.dataset.id = resp.id; }
          }).catch(()=>{});
        }
      });
    };

    projectList.appendChild(plusCol);
    // show plus card animation (slower)
    setTimeout(()=> plusCol.classList.add('show'), 40);

    // Load existing projects from server and render above the plus card
    (function loadProjects(){
      const params = new URLSearchParams(); params.append('action','get_projects');
      fetch('api.php',{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params.toString()})
      .then(res=>res.json())
      .then(resp=>{
        if(resp && resp.status==='success' && Array.isArray(resp.data)){
          resp.data.forEach((p,i)=>{
            const card = createProjectCard({id:p.id, title:p.title, description:p.description, github:p.github_link});
            projectList.insertBefore(card, plusCol);
            setTimeout(()=> card.classList.add('show'), 40 + (i * 120));
          });
        }
      }).catch(()=>{});
    })();

    // Ensure SweetAlert doesn't expand background: using backdrop:false already set on calls above

});
</script>


</body>
</html>
