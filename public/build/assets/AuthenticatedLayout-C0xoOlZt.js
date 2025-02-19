import{r as w,z as S,c as l,o as i,b as e,a as u,w as c,u as s,P as h,h as b,F as x,j as p,f as v,t as o,n as m,i as N,x as j,k}from"./app-BhfzyES2.js";const B={class:"min-h-screen bg-neutral-50 test"},C={class:"bg-white border-b border-neutral-200"},I={class:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"},M={class:"flex justify-between h-16"},$={class:"flex"},D={class:"flex-shrink-0 flex items-center"},L={class:"hidden space-x-8 sm:ml-10 sm:flex"},V={class:"hidden sm:ml-6 sm:flex sm:items-center space-x-4"},z={class:"relative"},A=["src","alt"],P={class:"absolute right-0 mt-2 w-48 rounded-lg shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-10",role:"menu"},F={class:"px-4 py-2 border-b border-neutral-200"},E={class:"text-sm font-medium text-neutral-900"},H={class:"text-xs text-neutral-500 truncate"},O={class:"py-1"},T={class:"-mr-2 flex items-center sm:hidden"},Y={class:"h-6 w-6",stroke:"currentColor",fill:"none",viewBox:"0 0 24 24"},q={class:"pt-2 pb-3 space-y-1"},G={class:"pt-4 pb-1 border-t border-neutral-200"},J={class:"flex items-center px-4"},K={class:"flex-shrink-0"},Q=["src","alt"],R={class:"ml-3"},U={class:"text-base font-medium text-neutral-800"},W={class:"text-sm font-medium text-neutral-500"},X={class:"mt-3 space-y-1"},Z={class:"py-10"},ee={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"},te={key:0,class:"mb-8"},se={class:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"},re={class:"px-4 sm:px-6 lg:px-8"},oe={__name:"AuthenticatedLayout",setup(ne){const n=w(!1),{auth:a}=S().props,_=[{name:"Dashboard",href:route("dashboard"),icon:"HomeIcon"}],g=[{name:"Songs",href:r=>route("songs.index",{band:r}),icon:"MusicNoteIcon"},{name:"Setlists",href:r=>route("setlists.index",{band:r}),icon:"ListBulletIcon"}],y=[{name:"Your Profile",href:route("profile.edit")},{name:"Settings",href:"#"},{name:"Sign out",href:route("logout"),method:"post"}],f=route().params.band;return(r,d)=>(i(),l("div",B,[e("nav",C,[e("div",I,[e("div",M,[e("div",$,[e("div",D,[u(s(h),{href:r.route("dashboard"),class:"flex"},{default:c(()=>d[2]||(d[2]=[e("img",{class:"h-8 w-auto",src:"/images/logo.svg",alt:"AI Setlist"},null,-1),e("img",{class:"h-8 w-auto",src:"/images/logo_text.svg",alt:"AI Setlist"},null,-1)])),_:1},8,["href"])]),e("div",L,[(i(),l(x,null,p(_,t=>u(s(h),{key:t.name,href:t.href,class:m([r.route().current(t.href)?"border-primary-500 text-neutral-900":"border-transparent text-neutral-500 hover:border-neutral-300 hover:text-neutral-700","inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200"])},{default:c(()=>[v(o(t.name),1)]),_:2},1032,["href","class"])),64)),s(f)?(i(),l(x,{key:0},p(g,t=>u(s(h),{key:t.name,href:t.href(s(f)),class:m([r.route().current(t.href(s(f)))?"border-primary-500 text-neutral-900":"border-transparent text-neutral-500 hover:border-neutral-300 hover:text-neutral-700","inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200"])},{default:c(()=>[v(o(t.name),1)]),_:2},1032,["href","class"])),64)):b("",!0)])]),e("div",V,[e("div",z,[e("button",{onClick:d[0]||(d[0]=t=>n.value=!n.value),class:"flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-neutral-300 transition duration-200"},[d[3]||(d[3]=e("span",{class:"sr-only"},"Open user menu",-1)),e("img",{class:"h-8 w-8 rounded-full object-cover",src:s(a).user.avatar||"https://ui-avatars.com/api/?name="+s(a).user.name,alt:s(a).user.name},null,8,A)]),N(e("div",P,[e("div",F,[e("p",E,o(s(a).user.name),1),e("p",H,o(s(a).user.email),1)]),e("div",O,[(i(),l(x,null,p(y,t=>u(s(h),{key:t.name,href:t.href,method:t.method,as:"button",class:"block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50",role:"menuitem"},{default:c(()=>[v(o(t.name),1)]),_:2},1032,["href","method"])),64))])],512),[[j,n.value]])])]),e("div",T,[e("button",{onClick:d[1]||(d[1]=t=>n.value=!n.value),class:"inline-flex items-center justify-center p-2 rounded-lg text-neutral-400 hover:text-neutral-500 hover:bg-neutral-100 focus:outline-none focus:bg-neutral-100 focus:text-neutral-500 transition duration-200"},[(i(),l("svg",Y,[e("path",{class:m({hidden:n.value,"inline-flex":!n.value}),"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M4 6h16M4 12h16M4 18h16"},null,2),e("path",{class:m({hidden:!n.value,"inline-flex":n.value}),"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M6 18L18 6M6 6l12 12"},null,2)]))])])])]),e("div",{class:m([{block:n.value,hidden:!n.value},"sm:hidden"])},[e("div",q,[(i(),l(x,null,p(_,t=>u(s(h),{key:t.name,href:t.href,class:m([r.route().current(t.href)?"bg-primary-50 border-primary-500 text-primary-700":"border-transparent text-neutral-600 hover:bg-neutral-50 hover:border-neutral-300 hover:text-neutral-800","block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors duration-200"])},{default:c(()=>[v(o(t.name),1)]),_:2},1032,["href","class"])),64)),s(f)?(i(),l(x,{key:0},p(g,t=>u(s(h),{key:t.name,href:t.href(s(f)),class:m([r.route().current(t.href(s(f)))?"bg-primary-50 border-primary-500 text-primary-700":"border-transparent text-neutral-600 hover:bg-neutral-50 hover:border-neutral-300 hover:text-neutral-800","block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors duration-200"])},{default:c(()=>[v(o(t.name),1)]),_:2},1032,["href","class"])),64)):b("",!0)]),e("div",G,[e("div",J,[e("div",K,[e("img",{class:"h-10 w-10 rounded-full object-cover",src:s(a).user.avatar||"https://ui-avatars.com/api/?name="+s(a).user.name,alt:s(a).user.name},null,8,Q)]),e("div",R,[e("div",U,o(s(a).user.name),1),e("div",W,o(s(a).user.email),1)])]),e("div",X,[(i(),l(x,null,p(y,t=>u(s(h),{key:t.name,href:t.href,method:t.method,class:"block px-4 py-2 text-base font-medium text-neutral-500 hover:text-neutral-800 hover:bg-neutral-100"},{default:c(()=>[v(o(t.name),1)]),_:2},1032,["href","method"])),64))])])],2)]),e("main",Z,[e("div",ee,[r.$slots.header?(i(),l("header",te,[e("div",se,[k(r.$slots,"header")])])):b("",!0),e("div",re,[k(r.$slots,"default")])])])]))}};export{oe as _};
