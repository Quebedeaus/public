document.addEventListener("DOMContentLoaded",(function(){let t,e,r=!1,n="";const a=document.getElementById("string-locator-search-notices"),o=document.getElementById("string-locator-progress-wrapper"),c=document.getElementById("string-locator-search-progress"),s=document.getElementById("string-locator-feedback-text"),i=document.getElementById("string-locator-search-form"),l=document.getElementById("string-locator-search"),d=document.getElementById("string-locator-string"),u=document.getElementById("string-locator-regex"),g=document.getElementById("string-locator-search-results-table-wrapper"),_=document.getElementById("string-locator-search-results-table"),h=document.getElementById("string-locator-search-results-tbody");function p(t,e,r){a.innerHTML+='<div class="notice notice-'+r+' is-dismissible"><p><strong>'+t+"</strong><br />"+e+"</p></div>"}function m(t,e){r=!1,o.style.display="none",p(t,e,"error")}function y(){a.innerHTML="",c.removeAttribute("value"),h.innerHTML=""}function f(a,i){if(e=new FormData,i>=a||!r)return s.innerHTML=string_locator.saving_results_string,r=!1,e=new FormData,s.innerText="",e.append("_wpnonce",string_locator.rest_nonce),fetch(string_locator.url.clean,{method:"POST",body:e}).then((function(){o.style.display="none",h.getElementsByTagName("tr").length<1&&(h.innerHTML='<tr><td colspan="3">'+string_locator.search_no_results+"</td></tr>")})).catch((function(t){m(t,string_locator.search_error)})),!1;e.append("filenum",i),e.append("_wpnonce",string_locator.rest_nonce),fetch(string_locator.url.search,{method:"POST",body:e}).then((t=>t.json())).then((function(e){if(!e.success){if(!1===e.data.continue)return m(string_locator.warning_title,e.data.message),!1;p(string_locator.warning_title,e.data.message,"warning")}void 0!==e.data.search&&(c.value=e.data.filenum,s.innerHTML=string_locator.search_current_prefix+e.data.next_file,n=void 0!==e.data.type?e.data.type:"",function(e){if(Array!==e.constructor)return!1;t=wp.template("string-locator-search-result"+(""!==n?"-"+n:"")),e.forEach((function(e){if(e)for(let r=0,n=e.length;r<n;r++){const n=e[r];void 0!==n.stringresult&&(h.innerHTML+=t(n))}}))}(e.data.search));const r=e.data.filenum+1;f(a,r)})).catch((function(t){m(t,string_locator.search_error)}))}i.addEventListener("submit",(function(t){if(t.preventDefault(),"sql"===l.value)return void function(t){t.preventDefault(),e=new FormData,s.innerText=string_locator.search_preparing,o.style.display="block",r=!0,y();const n=JSON.stringify({directory:l.value,search:d.value,regex:u.checked});_.style.display="table",g.style.display="block",e.append("data",n),e.append("_wpnonce",string_locator.rest_nonce),fetch(string_locator.url.directory_structure,{method:"POST",body:e}).then((t=>t.json())).then((function(t){t.success?(c.setAttribute("max",t.data.total),c.value=t.data.current,s.innerText=string_locator.search_started,f(t.data.total,0)):p("",t.data,"alert")})).catch((function(t){m(t,string_locator.search_error)}))}(t);e=new FormData,s.innerText=string_locator.search_preparing,o.style.display="block",r=!0,y();const n=JSON.stringify({directory:l.value,search:d.value,regex:u.checked});_.style.display="table",g.style.display="block",e.append("data",n),e.append("_wpnonce",string_locator.rest_nonce),fetch(string_locator.url.directory_structure,{method:"POST",body:e}).then((t=>t.json())).then((function(t){t.success?(c.setAttribute("max",t.data.total),c.value=t.data.current,s.innerText=string_locator.search_started,f(t.data.total,0)):p("",t.data,"alert")})).catch((function(t){m(t,string_locator.search_error)}))}))}));