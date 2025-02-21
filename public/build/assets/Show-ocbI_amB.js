import{c as r,o,a as k,w as u,b as t,u as m,f,t as i,F as g,j as C,n as B,h as d,e as p,P as b,r as L,m as V,W as z}from"./app-D8OPSLji.js";import{_ as A}from"./AuthenticatedLayout-DTlQNGiu.js";import{_}from"./Button-D80aJfgU.js";import{_ as D}from"./Card-BGvY5qee.js";import{_ as E}from"./AlertModal-BZPHqPCR.js";import{v as M}from"./visitExternalLink-CSLW9nbJ.js";import"./Modal-C9D4Vs5g.js";import"./transition-D3X4x4mI.js";const H={class:"space-y-6 flex justify-center"},U={class:"p-6"},q={class:"flex items-center justify-between mb-6"},P={class:"text-2xl font-bold text-white"},R={class:"mt-1 text-neutral-400"},I={class:"text-xl text-white"},N={class:"space-y-4"},F={class:"flex flex-col space-y-1.5"},O={class:"flex items-center justify-between"},T={class:"flex items-center space-x-2"},K={key:0,class:"flex items-center px-2 py-1 rounded-md bg-neutral-600 text-neutral-200"},W={class:"text-base font-semibold text-white"},G={class:"text-sm text-neutral-500"},J={key:0,class:"text-sm text-neutral-400 pl-4 -mt-1"},Q={key:1,class:"flex flex-wrap gap-1.5"},X=["onClick"],Y=["onClick"],Z={__name:"PerformanceMode",props:{band:{type:Object,required:!0},setlist:{type:Object,required:!0}},emits:["exit"],setup(l,{emit:$}){const x=l,w=c=>{const a=Math.floor(c/3600),y=Math.floor(c%3600/60),n=c%60;return a>0?`${a}:${y.toString().padStart(2,"0")}:${n.toString().padStart(2,"0")}`:`${y}:${n.toString().padStart(2,"0")}`},j=c=>{let a="https://www.google.com/search?q=";return a+=encodeURIComponent(c.name),c.artist&&(a+=" "+encodeURIComponent(c.artist)),a+="+lyrics",M(a,!0)},S=(c,a)=>{M(route("songs.files.download",[x.band.id,a.id,c.id]),!0,!0)};return(c,a)=>(o(),r("div",H,[k(m(D),{"bg-color":"bg-gray-900 h-full lg:w-1/2 w-full"},{default:u(()=>{var y;return[t("div",U,[k(m(_),{variant:"primary",onClick:a[0]||(a[0]=n=>c.$emit("exit")),class:"w-full sm:w-auto mb-4"},{default:u(()=>a[1]||(a[1]=[f(" Exit Performance Mode ")])),_:1}),t("div",q,[t("div",null,[t("h3",P,i(l.setlist.name),1),t("p",R,i(((y=l.setlist.items)==null?void 0:y.length)||0)+" items · "+i(w(l.setlist.total_duration)),1)]),t("div",I,i(w(l.setlist.total_duration)),1)]),t("div",N,[(o(!0),r(g,null,C(l.setlist.items,(n,e)=>(o(),r("div",{key:n.id,class:B(["rounded-lg border p-1",n.type==="break"?"border-neutral-600 bg-neutral-700/50":"border-neutral-700 bg-neutral-800/50"])},[t("div",F,[t("div",O,[t("div",T,[n.type==="break"?(o(),r("div",K,a[2]||(a[2]=[t("svg",{class:"h-4 w-4 mr-1",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 6v6m0 0v6m0-6h6m-6 0H6"})],-1),t("span",{class:"text-xs font-medium"},"BREAK",-1)]))):d("",!0),t("span",W,[n.type==="song"?(o(),r(g,{key:0},[f(i(l.setlist.items.filter(s=>s.type==="song"&&s.order<=n.order).length)+". ",1)],64)):d("",!0),f(" "+i(n.type==="break"?n.title:n.song.name),1)])]),t("span",G,i(w(n.duration_seconds)),1)]),n.notes?(o(),r("div",J,i(n.notes),1)):d("",!0),n.type==="song"?(o(),r("div",Q,[n.song.url?(o(),p(m(b),{key:0,href:n.song.url,target:"_blank",class:"inline-flex items-center px-2 py-1 text-xs font-medium text-primary-400 hover:text-primary-300"},{default:u(()=>a[3]||(a[3]=[f(" → Url ")])),_:2},1032,["href"])):d("",!0),t("button",{onClick:s=>j(n.song),class:"inline-flex items-center px-2 py-1 text-xs font-medium text-primary-400 hover:text-primary-300"}," ○ Lyrics ",8,X),(o(!0),r(g,null,C(n.song.files,s=>(o(),r("button",{key:s.id,onClick:v=>S(s,n.song),class:"inline-flex items-center px-2 py-1 text-xs font-medium text-primary-400 hover:text-primary-300"}," □ "+i(s.type.charAt(0).toUpperCase()+s.type.slice(1)),9,Y))),128))])):d("",!0)])],2))),128))])])]}),_:1})]))}},tt={class:"md:flex md:items-center md:justify-between"},et={key:0,class:"min-w-0 flex-1"},st={class:"flex items-center"},nt={class:"mt-1 text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight"},ot={class:"mt-1 text-sm text-neutral-500"},lt={class:"mt-4 flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3 md:ml-4 md:mt-0"},rt={class:"-ml-0.5 mr-1.5 h-4 w-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},it={key:0,"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"},at={key:1,"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M6 18L18 6M6 6l12 12"},dt={key:0,class:"grid gap-6 md:grid-cols-2"},ut={class:"p-6"},mt={class:"divide-y divide-neutral-200"},ct={class:"py-4"},xt={class:"mt-1 flex items-center text-sm text-neutral-900"},ft={class:"py-4"},ht={class:"mt-1 flex items-center text-sm text-neutral-900"},vt={class:"py-4"},kt={class:"mt-1 flex items-center text-sm text-neutral-900"},yt={class:"mt-6"},pt={class:"mt-2 text-sm text-neutral-900 whitespace-pre-line"},gt={class:"p-6"},wt={class:"flex items-center justify-between mb-4"},bt={class:"divide-y divide-neutral-200"},_t={class:"flex items-start justify-between"},$t={class:"min-w-0 flex-1"},Ct={class:"flex items-center"},Mt={key:0,class:"text-sm font-medium text-neutral-900"},jt={key:1,class:"flex items-center px-2 py-1 rounded-md bg-neutral-100 text-neutral-700"},St={class:"ml-2 text-xs text-neutral-500"},Bt={key:0,class:"mt-2 text-sm text-neutral-600"},Dt={key:0,class:"ml-4 flex items-center space-x-2"},Lt=["onClick"],Vt=["onClick"],Nt={__name:"Show",props:{band:{type:Object,required:!0},setlist:{type:Object,required:!0},isAdmin:{type:Boolean,required:!0}},setup(l){const $=l,x=L(!1),w=L(null),j=()=>{w.value.open()},S=()=>{z.delete(route("setlists.destroy",[$.band.id,$.setlist.id]))},c=n=>{const e=Math.floor(n/3600),s=Math.floor(n%3600/60),v=n%60;return e>0?`${e}:${s.toString().padStart(2,"0")}:${v.toString().padStart(2,"0")}`:`${s}:${v.toString().padStart(2,"0")}`},a=n=>{let e="https://www.google.com/search?q=";return e+=encodeURIComponent(n.name),n.artist&&(e+=" "+encodeURIComponent(n.artist)),e+="+lyrics",M(e,!0)},y=(n,e)=>{M(route("songs.files.download",[$.band.id,e.id,n.id]),!0,!0)};return(n,e)=>(o(),r(g,null,[k(m(V),{title:`${l.setlist.name} - ${l.band.name}`},null,8,["title"]),x.value?(o(),p(Z,{key:0,band:l.band,setlist:l.setlist,onExit:e[0]||(e[0]=s=>x.value=!1)},null,8,["band","setlist"])):(o(),p(A,{key:1},{header:u(()=>{var s,v;return[t("div",tt,[x.value?d("",!0):(o(),r("div",et,[t("div",st,[k(m(b),{href:n.route("bands.show",l.band.id),class:"text-sm font-medium text-primary-600 hover:text-primary-700"},{default:u(()=>[f(i(l.band.name),1)]),_:1},8,["href"]),e[2]||(e[2]=t("svg",{class:"mx-2 h-5 w-5 text-neutral-400",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M9 5l7 7-7 7"})],-1))]),t("h2",nt,i(l.setlist.name),1),t("p",ot,i(((s=l.setlist.items)==null?void 0:s.length)||0)+" items ("+i(((v=l.setlist.items)==null?void 0:v.filter(h=>h.type==="song").length)||0)+" songs) ",1)])),t("div",lt,[k(m(_),{variant:"primary",onClick:e[1]||(e[1]=h=>x.value=!x.value),class:"w-full sm:w-auto"},{default:u(()=>[(o(),r("svg",rt,[x.value?(o(),r("path",at)):(o(),r("path",it))])),f(" "+i(x.value?"Exit Performance Mode":"Performance Mode"),1)]),_:1}),l.isAdmin?(o(),r(g,{key:0},[k(m(b),{href:n.route("setlists.edit",[l.band.id,l.setlist.id]),class:"w-full sm:w-auto"},{default:u(()=>[x.value?d("",!0):(o(),p(m(_),{key:0,variant:"secondary",class:"w-full sm:w-auto"},{default:u(()=>e[3]||(e[3]=[t("svg",{class:"-ml-0.5 mr-1.5 h-4 w-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"})],-1),f(" Edit Setlist ")])),_:1}))]),_:1},8,["href"]),x.value?d("",!0):(o(),p(m(_),{key:0,variant:"danger",onClick:j,class:"w-full sm:w-auto"},{default:u(()=>e[4]||(e[4]=[t("svg",{class:"-ml-0.5 mr-1.5 h-4 w-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"})],-1),f(" Delete Setlist ")])),_:1}))],64)):d("",!0)])])]}),default:u(()=>[x.value?d("",!0):(o(),r("div",dt,[k(m(D),null,{default:u(()=>{var s,v;return[t("div",ut,[e[12]||(e[12]=t("div",{class:"flex items-center justify-between mb-4"},[t("h3",{class:"text-lg font-semibold text-neutral-900"},"About")],-1)),t("dl",mt,[t("div",ct,[e[6]||(e[6]=t("dt",{class:"text-sm font-medium text-neutral-500"},"Total Duration",-1)),t("dd",xt,[e[5]||(e[5]=t("svg",{class:"mr-1.5 h-5 w-5 flex-shrink-0 text-neutral-400",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"})],-1)),f(" "+i(c(l.setlist.total_duration)),1)])]),t("div",ft,[e[8]||(e[8]=t("dt",{class:"text-sm font-medium text-neutral-500"},"Songs",-1)),t("dd",ht,[e[7]||(e[7]=t("svg",{class:"mr-1.5 h-5 w-5 flex-shrink-0 text-neutral-400",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"})],-1)),f(" "+i(((s=l.setlist.items)==null?void 0:s.length)||0)+" items ("+i(((v=l.setlist.items)==null?void 0:v.filter(h=>h.type==="song").length)||0)+" songs) ",1)])]),t("div",vt,[e[10]||(e[10]=t("dt",{class:"text-sm font-medium text-neutral-500"},"Created",-1)),t("dd",kt,[e[9]||(e[9]=t("svg",{class:"mr-1.5 h-5 w-5 flex-shrink-0 text-neutral-400",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"})],-1)),f(" "+i(l.setlist.formatted_created_at),1)])])]),t("div",yt,[e[11]||(e[11]=t("h4",{class:"text-sm font-medium text-neutral-500"},"Description",-1)),t("p",pt,i(l.setlist.description||"No description provided."),1)])])]}),_:1}),k(m(D),null,{default:u(()=>[t("div",gt,[t("div",wt,[e[14]||(e[14]=t("h3",{class:"text-lg font-semibold text-neutral-900"},"Setlist Items",-1)),l.isAdmin?(o(),p(m(b),{key:0,href:n.route("setlists.edit",[l.band.id,l.setlist.id])},{default:u(()=>[k(m(_),{variant:"secondary",size:"sm"},{default:u(()=>e[13]||(e[13]=[f(" Edit Setlist ")])),_:1})]),_:1},8,["href"])):d("",!0)]),t("div",bt,[(o(!0),r(g,null,C(l.setlist.items,(s,v)=>(o(),r("div",{key:s.id,class:B(["py-4",s.type==="break"?"bg-neutral-50 -mx-6 px-6":""])},[t("div",_t,[t("div",$t,[t("div",Ct,[s.type==="song"?(o(),r("span",Mt,i(l.setlist.items.filter(h=>h.type==="song"&&h.order<=s.order).length)+". ",1)):d("",!0),s.type==="break"?(o(),r("div",jt,e[15]||(e[15]=[t("svg",{class:"h-4 w-4 mr-1",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 6v6m0 0v6m0-6h6m-6 0H6"})],-1),t("span",{class:"text-xs font-medium"},"BREAK",-1)]))):d("",!0),t("span",{class:B(["text-sm font-medium text-neutral-900",{"ml-2":s.type==="song"}])},i(s.type==="break"?s.title:s.song.name),3),t("span",St,i(c(s.duration_seconds)),1)]),s.notes?(o(),r("div",Bt,i(s.notes),1)):d("",!0)]),s.type==="song"?(o(),r("div",Dt,[s.song.url?(o(),p(m(b),{key:0,href:s.song.url,target:"_blank",class:"text-neutral-400 hover:text-neutral-500"},{default:u(()=>e[16]||(e[16]=[t("svg",{class:"h-5 w-5",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"})],-1)])),_:2},1032,["href"])):d("",!0),s.type==="song"?(o(),r("button",{key:1,onClick:h=>a(s.song),class:"text-neutral-400 hover:text-neutral-500"},e[17]||(e[17]=[t("svg",{class:"h-5 w-5",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"})],-1)]),8,Lt)):d("",!0),(o(!0),r(g,null,C(s.song.files,h=>(o(),r("button",{key:h.id,onClick:zt=>y(h,s.song),class:"text-neutral-400 hover:text-neutral-500"},e[18]||(e[18]=[t("svg",{class:"h-5 w-5",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"})],-1)]),8,Vt))),128))])):d("",!0)])],2))),128))])])]),_:1})])),k(m(E),{ref_key:"deleteModal",ref:w,title:"Delete Setlist",message:"Are you sure you want to delete this setlist? This action cannot be undone.",type:"error","confirm-text":"Delete","show-cancel":!0,onConfirm:S},null,512)]),_:1}))],64))}};export{Nt as default};
