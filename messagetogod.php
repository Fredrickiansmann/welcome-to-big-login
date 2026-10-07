<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Welcome to Big Messaging</title>
<style>
:root{box-sizing:border-box;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}
html{scroll-padding-top:env(safe-area-inset-top,0px)}
*,*::before,*::after{box-sizing:inherit}
body{background-color:rgb(62,57,82);color:white;font-family:system-ui,-apple-system,"Segoe UI",sans-serif;margin:0;padding:10px}
h1{font-size:50px;text-align:center;margin:20px 0 5px}
p{text-align:center}
.sub{font-size:18px;margin:0 0 20px}
.room{
  width:600px;max-width:100%;margin:auto;padding:20px;
  background-color:rgb(136,133,151);
  border-top-right-radius:0;border-bottom-left-radius:100px;
  border:8px solid white;
}
.descriptor{text-align:left;font-size:18px;display:block;margin:0 0 10px}
.inputtext{
  background-color:rgb(241,238,253);border:0;border-radius:10px;
  height:30px;padding:0 10px;color:rgb(62,57,82);font:inherit;font-size:15px;
}
#log{
  height:420px;overflow-y:auto;margin:0 0 20px;padding:10px;
  background-color:rgb(62,57,82);border-radius:20px;
  display:flex;flex-direction:column;gap:10px;
}
.msg{max-width:80%;padding:8px 14px;border-radius:15px;background:rgb(241,238,253);color:rgb(62,57,82);align-self:flex-start;word-wrap:break-word;overflow-wrap:anywhere}
.msg.me{align-self:flex-end;background:rgb(202,195,238)}
.who{font-size:12px;font-weight:bold;opacity:.7;display:block;margin-bottom:2px}
.empty{opacity:.7;margin:auto}
.row{display:flex;gap:10px;margin-bottom:10px}
#text{flex:1;min-width:0}
button{
  width:100%;height:60px;transition:all .5s;
  background-color:rgb(202,195,238);color:rgb(51,48,66);
  border:rgb(255,255,255) 5px solid;border-radius:20px;
  font:inherit;font-size:18px;cursor:pointer;margin-top:10px;
}
button:hover{background-color:rgb(66,61,90);color:white;border:5px solid rgb(66,61,90)}
button:disabled{opacity:.5;cursor:not-allowed}
#status{font-size:14px;margin:12px 0 0;min-height:1.2em}
@media (max-width:520px){h1{font-size:36px}.row{flex-direction:column}.room{border-top-right-radius:60px;border-bottom-left-radius:60px}}
</style>
</head>
<body>
<h1>Welcome To Big Messaging</h1>
<p class="sub">Talk about Skibidi, Phonk, And Habsburgs.</p>

<div class="room">
  <div id="log"><p class="empty">Loading messages…</p></div>
  <div class="row">
    <input id="text" class="inputtext" maxlength="255" placeholder="Type a message…" autocomplete="off">
  </div>
  <button id="send" type="button">Send</button>
  <p id="status"></p>
</div>

<script>
(function(){
  var log=document.getElementById('log'), textEl=document.getElementById('text'),
      sendBtn=document.getElementById('send'),
      status=document.getElementById('status');

  function render(msgs){
    var atBottom=log.scrollHeight-log.scrollTop-log.clientHeight<60;
    log.textContent='';
    if(!msgs.length){var p=document.createElement('p');p.className='empty';p.textContent='Type blad';log.appendChild(p);return;}
    msgs.forEach(function(m){
      var d=document.createElement('div'); d.className='msg';
      var w=document.createElement('span'); w.className='who'; w.textContent=m.name||'Anonymous';
      var t=document.createElement('span'); t.textContent=m.text||'';
      d.appendChild(w); d.appendChild(t); log.appendChild(d);
    });
    if(atBottom) log.scrollTop=log.scrollHeight;
  }

  async function send(){
    var text=textEl.value.trim(); if(!text) return;
    var body=new URLSearchParams({message:text});
    sendBtn.disabled=true;
    try{
      var response=await fetch('messageservice.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:body.toString()});
      var result=await response.json();
      if(!response.ok) throw new Error(result.error||'Could not send that message.');
      textEl.value='';
      status.textContent='';
      await loadMessages();
      textEl.focus();
    }catch(e){status.textContent=e.message||'Could not send that message. Please try again.';}
    finally{sendBtn.disabled=false;}
  }
  sendBtn.addEventListener('click',send);
  textEl.addEventListener('keydown',function(e){ if(e.key==='Enter'){e.preventDefault();send();} });

  async function loadMessages(){
    try{
      var response=await fetch('messageservice.php',{cache:'no-store'});
      var messages=await response.json();
      if(!response.ok) throw new Error(messages.error||'Could not load messages.');
      render(messages);
      if(status.textContent==='Could not load messages. Check the server connection.'){status.textContent='';}
    }catch(e){status.textContent='Could not load messages. Check the server connection.';}
  }
  render([]);
  loadMessages();
  window.setInterval(loadMessages,1000);
})();
</script>
</body>
</html>