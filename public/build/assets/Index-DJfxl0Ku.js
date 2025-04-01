import{A as H,x as M,r as C,c as u,o,a as i,u as r,m as N,w as s,b as e,h as p,F as k,e as f,f as a,P as m,j as B,t as v,W as $}from"./app-Dc_Fn3fE.js";import{_ as D}from"./AuthenticatedLayout-tG6DgGV4.js";import{_ as c}from"./DSTooltip.vue_vue_type_style_index_0_lang-B9_KI25U.js";import{_ as F}from"./Card-D-rXPIZf.js";import{_ as L}from"./AlertModal-BwEH6MaV.js";import{_ as y}from"./DSTooltip-B2bV6-Uj.js";import{v as P}from"./visitExternalLink-CSLW9nbJ.js";/* empty css            */import"./Alert-Bnj6zwPT.js";import"./Modal-ed41U4j-.js";import"./transition-Bm63RUDT.js";const q={class:"md:flex md:items-center md:justify-between"},T={class:"min-w-0 flex-1"},E={class:"flex items-center"},R={class:"mt-4 flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3 md:ml-4 md:mt-0"},U={class:"p-6"},W={key:0,class:"text-center py-12"},Y={class:"mt-6"},_={key:1},G={class:"overflow-x-auto"},I={class:"min-w-full divide-y divide-neutral-200"},O={class:"bg-white divide-y divide-neutral-200"},J={class:"px-6 py-4 whitespace-nowrap"},K={class:"px-6 py-4 whitespace-nowrap"},Q={class:"flex items-center text-sm text-neutral-500"},X={class:"px-6 py-4"},Z={class:"flex flex-wrap gap-2"},ee=["onClick"],te={class:"inline-flex items-center"},se=["href"],re={class:"px-6 py-4 whitespace-nowrap text-right text-sm"},oe={class:"flex justify-end items-center space-x-3"},ne=["onClick"],he={__name:"Index",props:{band:{type:Object,required:!0},songs:{type:Array,required:!0},isAdmin:{type:Boolean,required:!0}},setup(n){const h=n,A=H(),j=M(()=>A.props.trial||{}),w=M(()=>{var l;return(l=j.value)!=null&&l.isSubscribed?!0:h.songs.length<10}),g=C(null),b=C(null),S=l=>{b.value=l,g.value.open()},V=()=>{$.delete(route("songs.destroy",[h.band.id,b.value.id]))},z=(l,t)=>{P(route("songs.files.download",[h.band.id,t.id,l.id]),!0,!0)};return(l,t)=>(o(),u(k,null,[i(r(N),{title:`${n.band.name} - Songs`},null,8,["title"]),i(D,null,{header:s(()=>[e("div",q,[e("div",T,[e("div",E,[i(r(m),{href:l.route("bands.show",n.band.id),class:"text-sm font-medium text-primary-600 hover:text-primary-700"},{default:s(()=>[a(v(n.band.name),1)]),_:1},8,["href"]),t[0]||(t[0]=e("svg",{class:"mx-2 h-5 w-5 text-neutral-400",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M9 5l7 7-7 7"})],-1))]),t[1]||(t[1]=e("h2",{class:"mt-1 text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight"}," Songs ",-1)),t[2]||(t[2]=e("p",{class:"mt-1 text-sm text-neutral-500"}," Manage your band's song library ",-1))]),e("div",R,[i(r(m),{href:l.route("bands.show",n.band.id)},{default:s(()=>[i(r(c),{variant:"secondary",class:"w-full sm:w-auto"},{default:s(()=>t[3]||(t[3]=[e("svg",{class:"-ml-0.5 mr-1.5 h-4 w-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M10 19l-7-7m0 0l7-7m-7 7h18"})],-1),a(" Back to Band ")])),_:1})]),_:1},8,["href"]),n.isAdmin?(o(),u(k,{key:0},[w.value?(o(),f(r(m),{key:1,href:l.route("songs.create",n.band.id)},{default:s(()=>[i(r(c),{variant:"primary",class:"w-full sm:w-auto"},{default:s(()=>t[6]||(t[6]=[e("svg",{class:"-ml-0.5 mr-1.5 h-4 w-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 4v16m8-8H4"})],-1),a(" Add New Song ")])),_:1})]),_:1},8,["href"])):(o(),f(r(y),{key:0},{content:s(()=>t[5]||(t[5]=[a(" Free trial allows maximum 10 songs. Please subscribe to add more. ")])),default:s(()=>[i(r(c),{variant:"primary",class:"w-full sm:w-auto",disabled:""},{default:s(()=>t[4]||(t[4]=[e("svg",{class:"-ml-0.5 mr-1.5 h-4 w-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 4v16m8-8H4"})],-1),a(" Add New Song ")])),_:1})]),_:1})),w.value?(o(),f(r(m),{key:3,href:l.route("songs.bulk-create",n.band.id)},{default:s(()=>[i(r(c),{variant:"primary",class:"w-full sm:w-auto"},{default:s(()=>t[9]||(t[9]=[e("svg",{class:"-ml-0.5 mr-1.5 h-4 w-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"})],-1),a(" Add Multiple Songs ")])),_:1})]),_:1},8,["href"])):(o(),f(r(y),{key:2},{content:s(()=>t[8]||(t[8]=[a(" Free trial allows maximum 10 songs. Please subscribe to add more. ")])),default:s(()=>[i(r(c),{variant:"primary",class:"w-full sm:w-auto",disabled:""},{default:s(()=>t[7]||(t[7]=[e("svg",{class:"-ml-0.5 mr-1.5 h-4 w-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"})],-1),a(" Add Multiple Songs ")])),_:1})]),_:1}))],64)):p("",!0)])])]),default:s(()=>[i(r(F),null,{default:s(()=>[e("div",U,[n.songs.length===0?(o(),u("div",W,[t[13]||(t[13]=e("svg",{class:"mx-auto h-12 w-12 text-neutral-400",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"})],-1)),t[14]||(t[14]=e("h3",{class:"mt-2 text-sm font-medium text-neutral-900"},"No songs",-1)),t[15]||(t[15]=e("p",{class:"mt-1 text-sm text-neutral-500"},"Get started by creating your first song.",-1)),e("div",Y,[n.isAdmin?(o(),u(k,{key:0},[w.value?(o(),f(r(m),{key:1,href:l.route("songs.create",n.band.id)},{default:s(()=>[i(r(c),{variant:"primary"},{default:s(()=>t[12]||(t[12]=[a(" Add Your First Song ")])),_:1})]),_:1},8,["href"])):(o(),f(r(y),{key:0},{content:s(()=>t[11]||(t[11]=[a(" Free trial allows maximum 10 songs. Please subscribe to add more. ")])),default:s(()=>[i(r(c),{variant:"primary",disabled:""},{default:s(()=>t[10]||(t[10]=[a(" Add Your First Song ")])),_:1})]),_:1}))],64)):p("",!0)])])):(o(),u("div",_,[e("div",G,[e("table",I,[t[22]||(t[22]=e("thead",{class:"bg-neutral-50"},[e("tr",null,[e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider"}," Name "),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider"}," Duration "),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider"}," Resources "),e("th",{scope:"col",class:"px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider"}," Actions ")])],-1)),e("tbody",O,[(o(!0),u(k,null,B(n.songs,d=>(o(),u("tr",{key:d.id,class:"hover:bg-neutral-50"},[e("td",J,[i(r(m),{href:l.route("songs.show",[n.band.id,d.id]),class:"text-sm font-medium text-neutral-900 hover:text-primary-600"},{default:s(()=>[a(v(d.name),1)]),_:2},1032,["href"])]),e("td",K,[e("div",Q,[t[16]||(t[16]=e("svg",{class:"mr-1.5 h-4 w-4 flex-shrink-0 text-neutral-400",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"})],-1)),a(" "+v(d.formatted_duration),1)])]),e("td",X,[e("div",Z,[(o(!0),u(k,null,B(d.files,x=>(o(),u("div",{key:x.id,class:"inline-flex items-center rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-medium text-primary-700 cursor-pointer",onClick:le=>z(x,d)},[e("div",te,[t[17]||(t[17]=e("svg",{class:"-ml-0.5 mr-1.5 h-3 w-3",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"})],-1)),a(" "+v(x.type.charAt(0).toUpperCase()+x.type.slice(1)),1)])],8,ee))),128)),d.url?(o(),u("a",{key:0,href:d.url,target:"_blank",class:"inline-flex items-center rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-medium text-primary-700 hover:bg-primary-100"},t[18]||(t[18]=[e("svg",{class:"-ml-0.5 mr-1.5 h-3 w-3",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"})],-1),a(" URL ")]),8,se)):p("",!0)])]),e("td",re,[e("div",oe,[i(r(m),{href:l.route("songs.show",[n.band.id,d.id]),class:"text-neutral-400 hover:text-neutral-500"},{default:s(()=>t[19]||(t[19]=[e("svg",{class:"h-5 w-5",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M15 12a3 3 0 11-6 0 3 3 0 016 0z"}),e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"})],-1)])),_:2},1032,["href"]),n.isAdmin?(o(),f(r(m),{key:0,href:l.route("songs.edit",[n.band.id,d.id]),class:"text-neutral-400 hover:text-neutral-500"},{default:s(()=>t[20]||(t[20]=[e("svg",{class:"h-5 w-5",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"})],-1)])),_:2},1032,["href"])):p("",!0),n.isAdmin?(o(),u("button",{key:1,onClick:x=>S(d),class:"text-danger-400 hover:text-danger-500"},t[21]||(t[21]=[e("svg",{class:"h-5 w-5",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"})],-1)]),8,ne)):p("",!0)])])]))),128))])])])]))])]),_:1})]),_:1}),i(r(L),{ref_key:"deleteModal",ref:g,title:"Delete Song",message:"Are you sure you want to delete this song? This action cannot be undone.",type:"error","confirm-text":"Delete","show-cancel":!0,onConfirm:V},null,512)],64))}};export{he as default};
