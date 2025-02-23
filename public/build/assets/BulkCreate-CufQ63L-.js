import{C as I,r as x,l as z,c as d,o as i,a as o,u as t,m as D,w as n,b as e,d as F,e as C,h as w,F as k,j as U,t as g,f as m,P as V,a5 as P}from"./app-DaKYToEX.js";import{_ as q}from"./AuthenticatedLayout-CqZFDffC.js";import{_ as v}from"./Button-0r-NbAsR.js";import{_ as h}from"./Input-CkqyrGXc.js";import{_ as E}from"./Card-BWVHyMqj.js";import{_ as M}from"./Alert-DzQo1fGy.js";import{_ as O}from"./Modal-x5sIYfJb.js";import{_ as R}from"./DSDurationInput-CgxOlVbw.js";import{V as H}from"./transition-C7kDDkC6.js";const T={class:"md:flex md:items-center md:justify-between"},G={class:"min-w-0 flex-1"},X={class:"flex items-center"},J={class:"list-disc list-inside"},K={class:"overflow-x-auto"},Q={class:"min-w-[800px]"},W={class:"col-span-4"},Y={class:"col-span-2"},Z={class:"col-span-3"},ee={class:"col-span-2"},se={class:"col-span-1 flex justify-end items-center"},te=["onClick"],le={class:"p-4 md:p-6 flex flex-col sm:flex-row justify-between space-y-4 sm:space-y-0"},oe={class:"flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3"},ae={class:"flex space-x-3"},ne={key:0},re={key:1},ie={class:"space-y-4"},de={class:"mt-6 flex justify-end space-x-3"},ue={key:0},me={key:1},$=30,he={__name:"BulkCreate",props:{band:{type:Object,required:!0}},setup(p){const A=p,r=I({songs:[{name:"",duration:"",duration_seconds:"",url:"",artist:""}]});x({});const S=z(()=>r.songs.length<$),j=()=>{S.value&&r.songs.push({name:"",duration:"",duration_seconds:"",url:"",artist:""})},B=u=>{r.songs.length>1&&r.songs.splice(u,1)},y=x(!1),c=x(""),f=x(!1),_=x(null),L=async()=>{var u,s;if(c.value){f.value=!0,_.value=null;try{const b=(await P.post(route("spotify.playlist-tracks"),{playlist_url:c.value})).data.tracks;r.songs=b.map(a=>({name:a.name,duration:a.duration,duration_seconds:a.duration_seconds,url:a.url,artist:a.artist})),y.value=!1,c.value=""}catch(l){_.value=((s=(u=l.response)==null?void 0:u.data)==null?void 0:s.error)||"Failed to import playlist"}finally{f.value=!1}}},N=()=>{r.post(route("songs.bulk-store",A.band.id),{preserveScroll:!0,onSuccess:()=>{r.reset(),r.songs=[{name:"",duration:"",duration_seconds:"",url:"",artist:""}]}})};return(u,s)=>(i(),d(k,null,[o(t(D),{title:`Add Multiple Songs - ${p.band.name}`},null,8,["title"]),o(q,null,{header:n(()=>[e("div",T,[e("div",G,[e("div",X,[o(t(V),{href:u.route("bands.show",p.band.id),class:"text-sm font-medium text-primary-600 hover:text-primary-700"},{default:n(()=>[m(g(p.band.name),1)]),_:1},8,["href"]),s[5]||(s[5]=e("svg",{class:"mx-2 h-5 w-5 text-neutral-400",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M9 5l7 7-7 7"})],-1)),o(t(V),{href:u.route("songs.index",p.band.id),class:"text-sm font-medium text-primary-600 hover:text-primary-700"},{default:n(()=>s[4]||(s[4]=[m(" Songs ")])),_:1},8,["href"])]),s[6]||(s[6]=e("h2",{class:"mt-1 text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight"}," Add Multiple Songs ",-1)),e("p",{class:"mt-1 text-sm text-neutral-500"}," Add up to "+g($)+" songs at once ")])])]),default:n(()=>[o(t(E),null,{default:n(()=>[e("form",{onSubmit:F(N,["prevent"]),class:"divide-y divide-neutral-200"},[Object.keys(t(r).errors).length>0?(i(),C(t(M),{key:0,type:"error",class:"m-6"},{default:n(()=>[e("ul",J,[(i(!0),d(k,null,U(t(r).errors,l=>(i(),d("li",{key:l},g(l),1))),128))])]),_:1})):w("",!0),e("div",K,[e("div",Q,[s[8]||(s[8]=e("div",{class:"grid grid-cols-12 gap-3 px-3 py-2 bg-neutral-50 border-b"},[e("div",{class:"col-span-4"},"Song Name"),e("div",{class:"col-span-2"},"Duration"),e("div",{class:"col-span-3"},"Artist"),e("div",{class:"col-span-2"},"URL"),e("div",{class:"col-span-1"})],-1)),(i(!0),d(k,null,U(t(r).songs,(l,b)=>(i(),d("div",{key:b,class:"grid grid-cols-12 gap-3 px-3 py-2 border-b last:border-b-0"},[e("div",W,[o(t(h),{modelValue:l.name,"onUpdate:modelValue":a=>l.name=a,type:"text",placeholder:"Song name",required:"",class:"w-full"},null,8,["modelValue","onUpdate:modelValue"])]),e("div",Y,[o(t(R),{modelValue:l.duration,"onUpdate:modelValue":a=>l.duration=a,"onUpdate:seconds":a=>l.duration_seconds=a,label:null,placeholder:"Duration (MM:SS)",required:""},null,8,["modelValue","onUpdate:modelValue","onUpdate:seconds"])]),e("div",Z,[o(t(h),{modelValue:l.artist,"onUpdate:modelValue":a=>l.artist=a,type:"text",placeholder:"Artist name",class:"w-full"},null,8,["modelValue","onUpdate:modelValue"])]),e("div",ee,[o(t(h),{modelValue:l.url,"onUpdate:modelValue":a=>l.url=a,type:"url",placeholder:"https://",class:"w-full"},null,8,["modelValue","onUpdate:modelValue"])]),e("div",se,[t(r).songs.length>1?(i(),d("button",{key:0,type:"button",class:"p-2 text-neutral-400 hover:text-danger-500",onClick:a=>B(b)},s[7]||(s[7]=[e("svg",{class:"h-5 w-5",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"})],-1)]),8,te)):w("",!0)])]))),128))])]),e("div",le,[e("div",oe,[o(t(v),{type:"button",variant:"secondary",disabled:!S.value,onClick:j,class:"w-full sm:w-auto"},{default:n(()=>s[9]||(s[9]=[e("svg",{class:"-ml-0.5 mr-1.5 h-4 w-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 4v16m8-8H4"})],-1),m(" Add Another Song ")])),_:1},8,["disabled"]),o(t(v),{type:"button",variant:"secondary",onClick:s[0]||(s[0]=l=>y.value=!0),class:"w-full sm:w-auto"},{default:n(()=>s[10]||(s[10]=[e("svg",{class:"-ml-0.5 mr-1.5 h-4 w-4",viewBox:"0 0 24 24",fill:"currentColor"},[e("path",{d:"M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"})],-1),m(" Import from Spotify ")])),_:1})]),e("div",ae,[o(t(V),{href:u.route("songs.index",p.band.id),class:"flex-1 sm:flex-none"},{default:n(()=>[o(t(v),{variant:"outline",class:"w-full sm:w-auto"},{default:n(()=>s[11]||(s[11]=[m(" Cancel ")])),_:1})]),_:1},8,["href"]),o(t(v),{type:"submit",variant:"primary",disabled:t(r).processing,class:"flex-1 sm:w-auto"},{default:n(()=>[t(r).processing?(i(),d("span",ne,"Saving...")):(i(),d("span",re,"Save All Songs"))]),_:1},8,["disabled"])])])],32)]),_:1}),o(t(O),{show:y.value,onClose:s[3]||(s[3]=l=>y.value=!1),"max-width":"lg"},{default:n(()=>[o(t(H),{as:"h3",class:"text-lg font-medium leading-6 text-neutral-900 mb-4"},{default:n(()=>s[12]||(s[12]=[m(" Import from Spotify Playlist ")])),_:1}),e("div",ie,[s[13]||(s[13]=e("p",{class:"text-sm text-neutral-600"}," Enter a Spotify playlist URL or ID to import its tracks. ",-1)),_.value?(i(),C(t(M),{key:0,type:"error",class:"mb-4"},{default:n(()=>[m(g(_.value),1)]),_:1})):w("",!0),o(t(h),{modelValue:c.value,"onUpdate:modelValue":s[1]||(s[1]=l=>c.value=l),label:"Playlist URL",placeholder:"https://open.spotify.com/playlist/...",disabled:f.value},null,8,["modelValue","disabled"])]),e("div",de,[o(t(v),{variant:"outline",onClick:s[2]||(s[2]=l=>y.value=!1),disabled:f.value},{default:n(()=>s[14]||(s[14]=[m(" Cancel ")])),_:1},8,["disabled"]),o(t(v),{variant:"primary",onClick:L,disabled:!c.value||f.value},{default:n(()=>[f.value?(i(),d("span",ue,"Importing...")):(i(),d("span",me,"Import Tracks"))]),_:1},8,["disabled"])])]),_:1},8,["show"])]),_:1})],64))}};export{he as default};
