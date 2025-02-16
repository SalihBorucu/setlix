import{e as h,o as d,w as n,b as e,t as o,a,u as l,f as r,r as _,c as x,m as C,h as f,P as b,F as w,j as B,W as V}from"./app-CG_txIVR.js";import{_ as A}from"./AuthenticatedLayout-DJIiH2wS.js";import{_ as u}from"./Button-Kx0rHSGw.js";import{_ as k}from"./Card-BOaGNwsG.js";import{_ as S}from"./Modal-MVY_GAV0.js";import"./transition-Kkr64J0r.js";const $={class:"text-lg font-medium text-neutral-900"},M={class:"mt-3 text-sm text-neutral-600"},L={class:"mt-6 flex justify-end space-x-3"},j={__name:"ConfirmModal",props:{modelValue:{type:Boolean,required:!0},title:{type:String,required:!0},description:{type:String,required:!0},confirmText:{type:String,default:"Confirm"},cancelText:{type:String,default:"Cancel"},confirmVariant:{type:String,default:"danger"}},emits:["update:modelValue","confirm"],setup(s,{emit:g}){const v=g,m=()=>{v("update:modelValue",!1)},y=()=>{v("confirm"),m()};return(p,c)=>(d(),h(S,{show:s.modelValue,onClose:m},{default:n(()=>[e("div",null,[e("h2",$,o(s.title),1),e("p",M,o(s.description),1),e("div",L,[a(l(u),{variant:"outline",onClick:m},{default:n(()=>[r(o(s.cancelText),1)]),_:1}),a(l(u),{variant:s.confirmVariant,onClick:y},{default:n(()=>[r(o(s.confirmText),1)]),_:1},8,["variant"])])])]),_:1},8,["show"]))}},N={class:"md:flex md:items-center md:justify-between"},q={class:"min-w-0 flex-1"},z={class:"text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight"},T={class:"mt-1 text-sm text-neutral-500"},U={class:"mt-4 flex md:ml-4 md:mt-0 space-x-3"},D={class:"grid gap-6 md:grid-cols-2"},H={class:"p-6"},R={class:"space-y-4"},E={key:0,class:"aspect-video w-full overflow-hidden rounded-lg"},F=["src","alt"],P={class:"text-neutral-600"},W={class:"p-6"},I={class:"flex items-center justify-between mb-4"},O={class:"divide-y divide-neutral-200"},Q={class:"flex items-center"},Y=["src","alt"],G={class:"ml-3"},J={class:"text-sm font-medium text-neutral-900"},K={class:"text-xs text-neutral-500"},X={key:0,class:"inline-flex items-center rounded-full bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700"},Z={class:"p-6"},ee={class:"grid gap-4 sm:grid-cols-2 lg:grid-cols-4"},te={class:"relative group"},se={class:"h-full rounded-lg border border-neutral-200 p-6"},ne={class:"flex items-center justify-between"},le={class:"text-2xl font-semibold text-neutral-900"},ae={class:"mt-4 flex justify-between items-center"},ie={class:"relative group"},de={class:"h-full rounded-lg border border-neutral-200 p-6"},oe={class:"flex items-center justify-between"},re={class:"text-2xl font-semibold text-neutral-900"},ue={class:"mt-4 flex justify-between items-center"},he={__name:"Show",props:{band:{type:Object,required:!0},isAdmin:{type:Boolean,required:!0},currentUserRole:{type:String,required:!0}},setup(s){const g=s,v=_(!1),m=_(!1),y=()=>{V.delete(route("bands.destroy",g.band.id))},p=()=>{V.delete(route("bands.leave",g.band.id))};return(c,t)=>(d(),x(w,null,[a(l(C),{title:s.band.name},null,8,["title"]),a(A,null,{header:n(()=>[e("div",N,[e("div",q,[e("h2",z,o(s.band.name),1),e("p",T,o(s.band.members_count)+" members ",1)]),e("div",U,[!s.isAdmin&&s.currentUserRole==="member"?(d(),h(l(u),{key:0,variant:"danger",onClick:t[0]||(t[0]=i=>m.value=!0)},{default:n(()=>t[4]||(t[4]=[e("svg",{class:"-ml-0.5 mr-1.5 h-4 w-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"})],-1),r(" Leave Band ")])),_:1})):f("",!0),s.isAdmin?(d(),x(w,{key:1},[a(l(b),{href:c.route("bands.edit",s.band.id)},{default:n(()=>[a(l(u),{variant:"secondary"},{default:n(()=>t[5]||(t[5]=[e("svg",{class:"-ml-0.5 mr-1.5 h-4 w-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"})],-1),r(" Edit Band ")])),_:1})]),_:1},8,["href"]),a(l(u),{variant:"danger",onClick:t[1]||(t[1]=i=>v.value=!0)},{default:n(()=>t[6]||(t[6]=[e("svg",{class:"-ml-0.5 mr-1.5 h-4 w-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"})],-1),r(" Delete Band ")])),_:1})],64)):f("",!0)])])]),default:n(()=>[e("div",D,[a(l(k),null,{default:n(()=>[e("div",H,[t[7]||(t[7]=e("div",{class:"flex items-center justify-between mb-4"},[e("h3",{class:"text-lg font-semibold text-neutral-900"},"About")],-1)),e("div",R,[s.band.cover_image?(d(),x("div",E,[e("img",{src:s.band.cover_image,alt:s.band.name,class:"h-full w-full object-cover"},null,8,F)])):f("",!0),e("p",P,o(s.band.description||"No description provided."),1)])])]),_:1}),a(l(k),null,{default:n(()=>[e("div",W,[e("div",I,[t[9]||(t[9]=e("h3",{class:"text-lg font-semibold text-neutral-900"},"Members",-1)),s.isAdmin?(d(),h(l(b),{key:0,href:c.route("bands.members.index",s.band.id)},{default:n(()=>[a(l(u),{variant:"secondary",size:"sm"},{default:n(()=>t[8]||(t[8]=[r(" Manage Members ")])),_:1})]),_:1},8,["href"])):f("",!0)]),e("div",O,[(d(!0),x(w,null,B(s.band.members,i=>(d(),x("div",{key:i.id,class:"flex items-center justify-between py-3"},[e("div",Q,[e("img",{src:i.avatar||`https://ui-avatars.com/api/?name=${encodeURIComponent(i.name)}`,alt:i.name,class:"h-8 w-8 rounded-full"},null,8,Y),e("div",G,[e("p",J,o(i.name),1),e("p",K,o(i.email),1)])]),i.pivot.role==="admin"?(d(),x("span",X," Admin ")):f("",!0)]))),128))])])]),_:1}),a(l(k),{class:"md:col-span-2"},{default:n(()=>[e("div",Z,[t[18]||(t[18]=e("div",{class:"flex items-center justify-between mb-4"},[e("h3",{class:"text-lg font-semibold text-neutral-900"},"Quick Actions")],-1)),e("div",ee,[e("div",te,[e("div",se,[e("div",ne,[t[10]||(t[10]=e("h4",{class:"text-base font-medium text-neutral-900"},"Songs",-1)),e("span",le,o(s.band.songs_count||0),1)]),e("div",ae,[a(l(b),{href:c.route("songs.index",{band:s.band.id})},{default:n(()=>[a(l(u),{variant:"outline",size:"sm"},{default:n(()=>t[11]||(t[11]=[r(" View All ")])),_:1})]),_:1},8,["href"]),s.isAdmin?(d(),h(l(b),{key:0,href:c.route("songs.create",{band:s.band.id})},{default:n(()=>[a(l(u),{variant:"primary",size:"sm"},{default:n(()=>t[12]||(t[12]=[r(" Add New Song ")])),_:1})]),_:1},8,["href"])):f("",!0)])])]),e("div",ie,[e("div",de,[e("div",oe,[t[13]||(t[13]=e("h4",{class:"text-base font-medium text-neutral-900"},"Setlists",-1)),e("span",re,o(s.band.setlists_count||0),1)]),e("div",ue,[a(l(b),{href:c.route("setlists.index",{band:s.band.id})},{default:n(()=>[a(l(u),{variant:"outline",size:"sm"},{default:n(()=>t[14]||(t[14]=[r(" View All ")])),_:1})]),_:1},8,["href"]),s.isAdmin?(d(),h(l(b),{key:0,href:c.route("setlists.create",{band:s.band.id})},{default:n(()=>[a(l(u),{variant:"primary",size:"sm"},{default:n(()=>t[15]||(t[15]=[r(" Create New Setlist ")])),_:1})]),_:1},8,["href"])):f("",!0)])])]),t[16]||(t[16]=e("div",{class:"h-full rounded-lg border border-neutral-200 p-6"},[e("div",{class:"flex items-center justify-between"},[e("h4",{class:"text-base font-medium text-neutral-900"},"Upcoming Shows"),e("span",{class:"text-2xl font-semibold text-neutral-900"},"0")]),e("p",{class:"mt-1 text-sm text-neutral-500"},"Coming soon")],-1)),t[17]||(t[17]=e("div",{class:"h-full rounded-lg border border-neutral-200 p-6"},[e("div",{class:"flex items-center justify-between"},[e("h4",{class:"text-base font-medium text-neutral-900"},"Statistics"),e("span",{class:"text-2xl font-semibold text-neutral-900"},"-")]),e("p",{class:"mt-1 text-sm text-neutral-500"},"Coming soon")],-1))])])]),_:1})]),a(j,{modelValue:v.value,"onUpdate:modelValue":t[2]||(t[2]=i=>v.value=i),title:"Delete Band",description:"Are you sure you want to delete this band? This action cannot be undone and all associated data will be permanently deleted.","confirm-text":"Delete Band",onConfirm:y},null,8,["modelValue"]),a(j,{modelValue:m.value,"onUpdate:modelValue":t[3]||(t[3]=i=>m.value=i),title:"Leave Band",description:"Are you sure you want to leave this band? You will lose access to all band content and will need to be invited back to rejoin.","confirm-text":"Leave Band",onConfirm:p},null,8,["modelValue"])]),_:1})],64))}};export{he as default};
