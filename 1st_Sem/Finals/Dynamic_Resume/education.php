<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <title>Education | My Portfolio</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Icons & Fonts -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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

    /* TIMELINE */
    .timeline {
      position: relative;
      padding-left: 2rem;
    }

    /* FADED LINE */
    .timeline::before {
      content: "";
      position: absolute;
      top: 0;
      left: 1rem;
      width: 4px;
      height: 100%;
      background: linear-gradient(white, transparent);
      border-radius: 2px;
    }

    .timeline-item {
      position: relative;
      padding-left: 2rem;
      margin-bottom: 2rem;
    }

    /* transparent timeline card (no glass) */
    .timeline-item.transparent {
      background: transparent !important;
      backdrop-filter: none !important;
      box-shadow: none !important;
      padding: 0.25rem 0.5rem;
      border-radius: 6px;
    }

    .timeline-item::before {
      content: "❁";
      position: absolute;
      left: -0.5rem;
      top: 0;
      font-size: 1.5rem;
    }

    /* SEMINAR TITLE */
    .seminar-title {
      background: rgba(255,255,255,0.2);
      padding: 0.6rem 1rem;
      border-radius: 10px;
      margin-bottom: 1rem;
      font-weight: 600;
      text-align: center;
    }

    .footer-icon {
      color: #fff;
      font-size: 1.4rem;
      transition: transform 0.3s ease;
    }
    .footer-icon:hover { transform: scale(1.2); }

    /* seminar card animations and add button (slower) */
    .skill-animate { opacity: 0; transform: translateY(12px); transition: transform .8s cubic-bezier(.2,.9,.2,1), opacity .8s ease; }
    .skill-animate.show { opacity: 1; transform: translateY(0); }
    .add-seminar { display:flex; justify-content:center; align-items:center; height:100%; font-size:2rem; cursor:pointer; }

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
        <a class="nav-link" href="projects.php">Projects</a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="education.php">Education</a>
      </li>
    </ul>
  </nav>

  <!-- EDUCATION + WORK EXPOSURE COMBINED -->
  <div class="glass max-w-6xl mx-auto mb-5 skill-animate">
    <h1 class="fw-bold text-center mb-4 heading-text" style="font-size: 2rem;">Education and Work Exposure</h1>

    <div class="row g-4">

      <!-- LEFT: EDUCATION -->
        <div class="col-md-6">
          <h3 class="fw-semibold text-center mb-3">Education</h3>
          <div id="educationTimeline" class="timeline">
            <!-- dynamic education entries will be loaded here -->
          </div>
          <div class="mt-3 text-center">
            <div id="addEducationBtn" class="glass d-inline-block p-2" style="cursor:pointer;">
              <i class="bi bi-plus-lg"></i> Add Education
            </div>
          </div>
        </div>

      <!-- RIGHT: WORK EXPOSURE (NO DESCRIPTIONS) -->
      <div class="col-md-6">
        <h3 class="fw-semibold text-center mb-3">Work Exposure</h3>
        <div id="workTimeline" class="timeline">
          <!-- dynamic work exposure entries will be loaded here -->
        </div>
        <div class="mt-3 text-center">
          <div id="addWorkBtn" class="glass d-inline-block p-2" style="cursor:pointer;">
            <i class="bi bi-plus-lg"></i> Add Work Exposure
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- SEMINARS SECTION -->
    <div class="glass max-w-6xl mx-auto mb-5 p-4 skill-animate">
      <h1 class="fw-bold text-center mb-4 heading-text" style="font-size: 2rem;">Seminars and Trainings</h1>
      <p class="opacity-90 text-center">
        <i>Attended industry-focused seminars to enhance technical skills and professional growth.</i>
      </p>
    </div>

  <div class="max-w-6xl mx-auto">
    <div class="row g-4 seminar-grid">
      <!-- seminar cards + plus card will be injected here -->
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
    const api = 'api.php';

    function renderEntryMarkup(item){
      // title bold, description italic; use transparent card
      return `<div class="timeline-item transparent p-3 mb-3" data-id="${item.id}">`+
             `<div><strong>${escapeHtml(item.title)}</strong></div>`+
             `<div><em>${escapeHtml(item.description)}</em></div>`+
             `</div>`;
    }

    function escapeHtml(s){ if(!s) return ''; return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function loadEducation(){
      const params = new URLSearchParams(); params.append('action','get_education');
      fetch(api,{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params.toString()})
      .then(r=>r.json()).then(resp=>{
        const el = document.getElementById('educationTimeline'); el.innerHTML='';
        if(resp && resp.status==='success'){
          resp.data.forEach(it=> el.insertAdjacentHTML('beforeend', renderEntryMarkup(it)));
        }
        attachEntryHandlers('educationTimeline','update_education','delete_education');
      }).catch(()=>{});
    }

    function loadWork(){
      const params = new URLSearchParams(); params.append('action','get_work_exposure');
      fetch(api,{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params.toString()})
      .then(r=>r.json()).then(resp=>{
        const el = document.getElementById('workTimeline'); el.innerHTML='';
        if(resp && resp.status==='success'){
          resp.data.forEach(it=> el.insertAdjacentHTML('beforeend', renderEntryMarkup(it)));
        }
        attachEntryHandlers('workTimeline','update_work_exposure','delete_work_exposure');
      }).catch(()=>{});
    }

    function attachEntryHandlers(containerId, updateAction, deleteAction){
      const container = document.getElementById(containerId);
      container.querySelectorAll('.timeline-item').forEach(item=>{
        item.ondblclick = ()=>{
          const id = item.dataset.id;
          Swal.fire({
            title: 'Entry Actions',
            showDenyButton:true,
            showCancelButton:true,
            confirmButtonText:'Edit',
            denyButtonText:'Delete',
            backdrop:false
          }).then(r=>{
            if(r.isConfirmed){
              Swal.fire({
                title:'Edit Entry',
                html:`<input id="t" class="swal2-input" value="${item.querySelector('strong').innerText}">`+
                     `<textarea id="d" class="swal2-textarea">${item.querySelector('em').innerText}</textarea>`,
                showCancelButton:true,
                backdrop:false,
                preConfirm:()=>({t:document.getElementById('t').value,d:document.getElementById('d').value})
              }).then(res=>{
                if(res.isConfirmed){
                  const p = new URLSearchParams(); p.append('action', updateAction); p.append('id', id); p.append('title', res.value.t); p.append('description', res.value.d);
                  fetch(api,{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: p.toString()}).then(()=>{ if(containerId==='educationTimeline') loadEducation(); else loadWork(); });
                }
              });
            }
            if(r.isDenied){
              Swal.fire({title:'Delete entry?', showCancelButton:true, backdrop:false}).then(x=>{ if(x.isConfirmed){ const p=new URLSearchParams(); p.append('action', deleteAction); p.append('id', id); fetch(api,{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: p.toString()}).then(()=>{ if(containerId==='educationTimeline') loadEducation(); else loadWork(); }); } });
            }
          });
        };
      });
    }

    // Add handlers for add buttons and initialize lists
    document.addEventListener('DOMContentLoaded', ()=>{
      // animate all elements with .skill-animate (slower, staggered)
      document.querySelectorAll('.skill-animate').forEach((el,i)=>{
        setTimeout(()=> el.classList.add('show'), 40 + (i * 160));
      });
      document.getElementById('addEducationBtn').onclick = ()=>{
        Swal.fire({
          title:'Add Education',
          html:`<input id="t" class="swal2-input" placeholder="Title">`+
               `<textarea id="d" class="swal2-textarea" placeholder="Description"></textarea>`,
          showCancelButton:true,
          backdrop:false,
          preConfirm:()=>({t:document.getElementById('t').value,d:document.getElementById('d').value})
        }).then(r=>{ if(r.isConfirmed){ const p=new URLSearchParams(); p.append('action','add_education'); p.append('title', r.value.t); p.append('description', r.value.d); fetch(api,{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: p.toString()}).then(()=>loadEducation()); } });
      };

      document.getElementById('addWorkBtn').onclick = ()=>{
        Swal.fire({
          title:'Add Work Exposure',
          html:`<input id="t" class="swal2-input" placeholder="Title">`+
               `<textarea id="d" class="swal2-textarea" placeholder="Description"></textarea>`,
          showCancelButton:true,
          backdrop:false,
          preConfirm:()=>({t:document.getElementById('t').value,d:document.getElementById('d').value})
        }).then(r=>{ if(r.isConfirmed){ const p=new URLSearchParams(); p.append('action','add_work_exposure'); p.append('title', r.value.t); p.append('description', r.value.d); fetch(api,{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: p.toString()}).then(()=>loadWork()); } });
      };

      loadEducation();
      loadWork();
      loadSeminars();
    });

    /* ===== Seminars: dynamic cards + plus button (match skills behavior) ===== */
    function renderSeminarCard(item){
      const col = document.createElement('div'); col.className='col-md-6';
      col.innerHTML = `<div class="glass h-100 skill-animate" data-id="${item.id}" style="cursor:pointer;">
                        <h5 class="fw-semibold mb-2"><strong>${escapeHtml(item.title)}</strong></h5>
                        <p class="opacity-90"><em>${escapeHtml(item.description)}</em></p>
                       </div>`;
      const glass = col.querySelector('.glass');
      glass.ondblclick = ()=>{
        Swal.fire({ title:'Seminar Actions', showDenyButton:true, showCancelButton:true, confirmButtonText:'Edit', denyButtonText:'Delete', backdrop:false }).then(r=>{
          if(r.isConfirmed){
            Swal.fire({ title:'Edit Seminar', html:`<input id="t" class="swal2-input" value="${escapeHtml(item.title)}">`+`<textarea id="d" class="swal2-textarea">${escapeHtml(item.description)}</textarea>`, showCancelButton:true, backdrop:false, preConfirm:()=>({t:document.getElementById('t').value,d:document.getElementById('d').value}) }).then(res=>{ if(res.isConfirmed){ const p=new URLSearchParams(); p.append('action','update_seminar'); p.append('id', item.id); p.append('title', res.value.t); p.append('description', res.value.d); fetch(api,{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: p.toString()}).then(()=>loadSeminars()); } });
          }
          if(r.isDenied){ Swal.fire({title:'Delete seminar?', showCancelButton:true, backdrop:false}).then(x=>{ if(x.isConfirmed){ const p=new URLSearchParams(); p.append('action','delete_seminar'); p.append('id', item.id); fetch(api,{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: p.toString()}).then(()=>loadSeminars()); } }); }
        });
      };
      setTimeout(()=> col.querySelector('.skill-animate').classList.add('show'), 60);
      document.querySelector('.seminar-grid').appendChild(col);
    }

    function addSeminarPlus(){
      const col=document.createElement('div'); col.className='col-md-6';
      col.innerHTML = `<div class="glass h-100 add-seminar skill-animate d-flex justify-content-center align-items-center" style="font-size:2rem;cursor:pointer;"><i class="bi bi-plus-lg"></i></div>`;
      col.querySelector('.add-seminar').onclick = ()=>{
        Swal.fire({ title:'Add Seminar', html:`<input id="t" class="swal2-input" placeholder="Title">`+`<textarea id="d" class="swal2-textarea" placeholder="Description"></textarea>`, showCancelButton:true, backdrop:false, preConfirm:()=>({t:document.getElementById('t').value,d:document.getElementById('d').value}) }).then(r=>{ if(r.isConfirmed){ const p=new URLSearchParams(); p.append('action','add_seminar'); p.append('title', r.value.t); p.append('description', r.value.d); fetch(api,{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: p.toString()}).then(()=>loadSeminars()); } });
      };
      document.querySelector('.seminar-grid').appendChild(col);
      setTimeout(()=> col.querySelector('.skill-animate').classList.add('show'), 60);
    }

    function loadSeminars(){
      const grid = document.querySelector('.seminar-grid');
      if(!grid) return;
      grid.innerHTML='';
      const params = new URLSearchParams(); params.append('action','get_seminars');
      fetch(api,{method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params.toString()}).then(r=>r.json()).then(resp=>{
        if(resp && resp.status==='success'){ resp.data.forEach(s=> renderSeminarCard(s)); }
        addSeminarPlus();
      }).catch(()=>{ addSeminarPlus(); });
    }
  </script>
</body>
</html>
