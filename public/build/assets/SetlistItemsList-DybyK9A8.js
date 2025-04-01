import{x as $,e as g,o as a,w as m,b as e,r as S,c as d,a as c,G as h,h as p,f as b,t as u,u as x,P as y,i as C,n as k,s as j,F as M,j as B,H as V}from"./app-By7LNvvF.js";import{_ as N}from"./Modal-C-Af_cyN.js";import{_ as w}from"./Input-eS0M8yT6.js";import{_ as q}from"./Card-BxvmkG30.js";import{_ as A}from"./DSDurationInput-7Wvo9swi.js";import{_}from"./DSTooltip.vue_vue_type_style_index_0_lang-DhlZjsMt.js";import{d as D}from"./BreakModal-Dt3HaUUG.js";const H={class:"space-y-4"},T={class:"flex rounded-md shadow-sm"},z=["value"],O={class:"flex justify-end"},U={__name:"ShareSetlistModal",props:{show:{type:Boolean,required:!0},setlist:{type:Object,required:!0}},emits:["close"],setup(s){const l=s,i=$(()=>route("public.setlist.show",l.setlist.public_slug)),o=async()=>{try{await navigator.clipboard.writeText(i.value);const r=document.createElement("div");r.className="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded",r.innerHTML=`
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Link copied to clipboard!</span>
            </div>
        `,document.body.appendChild(r),setTimeout(()=>{r.remove()},3e3)}catch(r){console.error("Failed to copy text: ",r)}};return(r,t)=>(a(),g(N,{show:s.show,onClose:t[1]||(t[1]=n=>r.$emit("close")),title:"Share Setlist with Client","max-width":"md"},{footer:m(()=>[e("div",O,[e("button",{type:"button",class:"inline-flex justify-center rounded-md border border-transparent bg-indigo-100 px-4 py-2 text-sm font-medium text-indigo-900 hover:bg-indigo-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2",onClick:t[0]||(t[0]=n=>r.$emit("close"))}," Close ")])]),default:m(()=>[e("div",H,[t[2]||(t[2]=e("p",{class:"text-sm text-gray-500"}," Share this link with your client to let them customize the setlist: ",-1)),e("div",T,[e("input",{type:"text",value:i.value,readonly:"",class:"block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"},null,8,z),e("button",{onClick:o,class:"ml-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"}," Copy ")])])]),_:1},8,["show"]))}},L={class:"flex items-center space-x-4"},P={key:0,class:"flex items-center space-x-2"},Q={key:1},I={__name:"PublicAccessToggle",props:{setlist:Object,band:Object},setup(s){const l=s,i=S(!1),o=()=>{h.post(route("setlists.make-public",[l.band.id,l.setlist.id])).then(n=>{l.setlist.is_public=!0,l.setlist.public_slug=n.data.setlist.public_slug,i.value=!0})},r=()=>{h.post(route("setlists.make-private",[l.band.id,l.setlist.id])).then(n=>{l.setlist.is_public=!1,l.setlist.public_slug=null})},t=()=>{i.value=!1};return(n,f)=>(a(),d("div",L,[s.setlist.is_public?(a(),d("div",P,[f[0]||(f[0]=e("span",{class:"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"}," Shared with Client ",-1)),e("button",{onClick:r,class:"text-sm text-gray-600 hover:text-gray-900"}," Stop Sharing ")])):(a(),d("div",Q,[e("button",{onClick:o,class:"inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"}," Share with Client ")])),c(U,{show:i.value,setlist:s.setlist,onClose:t},null,8,["show","setlist"])]))}},E={class:"md:flex md:items-center md:justify-between"},F={class:"min-w-0 flex-1"},R={class:"flex items-center"},G={class:"mt-1 text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight"},K={key:0,class:"mt-1 text-sm text-neutral-500"},J={class:"mt-4 flex md:ml-4 md:mt-0"},qe={__name:"SetlistHeader",props:{band:{type:Object,required:!0},setlist:{type:Object,required:!1,default:null}},setup(s){return(l,i)=>{var o;return a(),d("div",E,[e("div",F,[e("div",R,[c(x(y),{href:l.route("bands.show",s.band.id),class:"text-sm font-medium text-primary-600 hover:text-primary-700"},{default:m(()=>[b(u(s.band.name),1)]),_:1},8,["href"]),i[1]||(i[1]=e("svg",{class:"mx-2 h-5 w-5 text-neutral-400",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M9 5l7 7-7 7"})],-1)),c(x(y),{href:l.route("setlists.index",s.band.id),class:"text-sm font-medium text-primary-600 hover:text-primary-700"},{default:m(()=>i[0]||(i[0]=[b(" Setlists ")])),_:1},8,["href"])]),e("h2",G,u(s.setlist?s.setlist.name:"Create New Setlist"),1),(o=s.setlist)!=null&&o.description?(a(),d("p",K,u(s.setlist.description),1)):p("",!0)]),e("div",J,[s.setlist?(a(),g(I,{key:0,band:s.band,setlist:s.setlist},null,8,["band","setlist"])):p("",!0)])])}}},W={class:"p-6 space-y-6"},X={class:"mt-1"},Ae={__name:"SetlistBasicInfo",props:{form:{type:Object,required:!0}},emits:["update:targetDuration"],setup(s,{emit:l}){return(i,o)=>(a(),g(x(q),null,{default:m(()=>[e("div",W,[e("div",null,[c(x(w),{modelValue:s.form.name,"onUpdate:modelValue":o[0]||(o[0]=r=>s.form.name=r),type:"text",label:"Setlist Name",error:s.form.errors.name,required:""},null,8,["modelValue","error"]),o[4]||(o[4]=e("p",{class:"mt-1 text-sm text-neutral-500"}," Give your setlist a memorable name ",-1))]),e("div",null,[o[5]||(o[5]=e("label",{class:"block text-sm font-medium text-neutral-700"}," Description (Optional) ",-1)),e("div",X,[C(e("textarea",{"onUpdate:modelValue":o[1]||(o[1]=r=>s.form.description=r),rows:"3",class:k(["block w-full rounded-lg shadow-sm transition-colors duration-200","border-neutral-300 focus:border-primary-500 focus:ring-primary-500",{"border-error-500 focus:border-error-500 focus:ring-error-500":s.form.errors.description}])},null,2),[[j,s.form.description]])]),o[6]||(o[6]=e("p",{class:"mt-1 text-sm text-neutral-500"}," Add any notes or context about this setlist ",-1))]),e("div",null,[c(x(A),{modelValue:s.form.target_duration,"onUpdate:modelValue":o[2]||(o[2]=r=>s.form.target_duration=r),"onUpdate:seconds":o[3]||(o[3]=r=>s.form.target_duration_seconds=r),label:"Target Duration",error:s.form.errors.target_duration,required:""},null,8,["modelValue","error"]),o[7]||(o[7]=e("p",{class:"mt-1 text-sm text-neutral-500"}," Set the target duration for this event ",-1))])])]),_:1}))}},Y={class:"flex items-center justify-between mb-4"},Z={class:"text-sm text-neutral-500"},ee={class:"mb-4"},te={class:"min-h-[400px] rounded-lg border-2 border-dashed border-neutral-200 p-4 bg-neutral-50 overflow-y-auto"},se={class:"flex items-center"},re={class:"font-medium text-neutral-900"},oe={class:"flex items-center space-x-3"},ne={class:"text-sm text-neutral-500"},ie=["onClick"],le={key:0,class:"text-center py-12"},ae={class:"mt-1 text-sm text-neutral-500"},_e={__name:"AvailableSongsList",props:{songs:{type:Array,required:!0},searchQuery:{type:String,required:!0}},emits:["update:searchQuery","addSong"],setup(s,{emit:l}){const i=l,o=r=>{const t=Math.floor(r/60),n=r%60;return`${t}:${n.toString().padStart(2,"0")}`};return(r,t)=>(a(),d("div",null,[e("div",Y,[t[1]||(t[1]=e("h3",{class:"text-lg font-medium text-neutral-900"},"Available Songs",-1)),e("span",Z,u(s.songs.length)+" songs ",1)]),e("div",ee,[c(x(w),{"model-value":s.searchQuery,"onUpdate:modelValue":t[0]||(t[0]=n=>i("update:searchQuery",n)),type:"text",placeholder:"Search songs...",class:"w-full"},{prefix:m(()=>t[2]||(t[2]=[e("svg",{class:"h-5 w-5 text-neutral-400",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"})],-1)])),_:1},8,["model-value"])]),e("div",te,[(a(!0),d(M,null,B(s.songs,n=>(a(),d("div",{key:n.id,class:"flex items-center justify-between p-3 mb-2 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200"},[e("div",se,[t[3]||(t[3]=e("svg",{class:"h-5 w-5 text-neutral-400 mr-2",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"})],-1)),e("span",re,u(n.name),1)]),e("div",oe,[e("span",ne,u(o(n.duration_seconds)),1),e("button",{type:"button",onClick:f=>i("addSong",n),class:"p-1 rounded-full text-primary-600 hover:bg-primary-50",title:"Add to setlist"},t[4]||(t[4]=[e("svg",{class:"h-5 w-5",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 4v16m8-8H4"})],-1)]),8,ie)])]))),128)),s.songs.length===0?(a(),d("div",le,[t[5]||(t[5]=e("svg",{class:"mx-auto h-12 w-12 text-neutral-400",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"})],-1)),t[6]||(t[6]=e("h3",{class:"mt-2 text-sm font-medium text-neutral-900"},"No songs found",-1)),e("p",ae,u(s.searchQuery?"Try a different search term.":"All songs are in the setlist."),1)])):p("",!0)])]))}},de={class:"mb-4"},ue={class:"flex items-center justify-between mb-2"},me={class:"flex items-center"},ce={class:"flex items-center justify-center w-6 h-6 rounded-full bg-primary-100 text-primary-700 text-sm font-medium mr-2"},fe={class:"flex items-center"},xe={key:0,class:"flex items-center px-2 py-1 rounded-md bg-neutral-100 text-neutral-700 mr-2"},pe={class:"font-medium text-neutral-900"},be={class:"flex items-center space-x-3"},ve={class:"text-sm text-neutral-500"},ge={class:"mt-2"},he=["value","placeholder"],ye={__name:"SetlistItem",props:{item:{type:Object,required:!0},index:{type:Number,required:!0}},emits:["remove","updateNotes"],setup(s,{emit:l}){const i=l,o=r=>{const t=Math.floor(r/60),n=r%60;return`${t}:${n.toString().padStart(2,"0")}`};return(r,t)=>(a(),d("div",de,[e("div",{class:k(["p-3 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200",s.item.type==="break"?"bg-neutral-50 border-2 border-dashed border-neutral-200":"bg-white border border-neutral-200"])},[e("div",ue,[e("div",me,[t[3]||(t[3]=e("span",{class:"drag-handle cursor-move p-1 hover:bg-neutral-50 rounded mr-2"},[e("svg",{class:"h-5 w-5 text-neutral-400",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M4 8h16M4 16h16"})])],-1)),e("span",ce,u(s.index+1),1),e("div",fe,[s.item.type==="break"?(a(),d("div",xe,t[2]||(t[2]=[e("svg",{class:"h-4 w-4 mr-1",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 6v6m0 0v6m0-6h6m-6 0H6"})],-1),e("span",{class:"text-xs font-medium"},"BREAK",-1)]))):p("",!0),e("span",pe,u(s.item.type==="break"?s.item.title:s.item.name),1)])]),e("div",be,[e("span",ve,u(o(s.item.duration_seconds)),1),e("button",{type:"button",onClick:t[0]||(t[0]=n=>i("remove",s.index)),class:"p-1 rounded-full text-error-600 hover:bg-error-50",title:"Remove from setlist"},t[4]||(t[4]=[e("svg",{class:"h-5 w-5",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M20 12H4"})],-1)]))])]),e("div",ge,[e("input",{type:"text",value:s.item.pivot.notes,onInput:t[1]||(t[1]=n=>i("updateNotes",{index:s.index,notes:n.target.value})),placeholder:s.item.type==="break"?"Add break notes...":"Add performance notes...",class:"block w-full text-sm rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500"},null,40,he)])],2)]))}},ke={class:"flex justify-end mb-4"},we={class:"flex items-center justify-between mb-4"},$e={class:"flex items-center text-sm text-neutral-500"},De={__name:"SetlistItemsList",props:{items:{type:Array,required:!0},totalDuration:{type:Number,required:!0}},emits:["update:items","remove","updateNotes","addBreak"],setup(s,{emit:l}){const i=l,o=r=>{const t=Math.floor(r/3600),n=Math.floor(r%3600/60),f=r%60;return t>0?`${t}:${n.toString().padStart(2,"0")}:${f.toString().padStart(2,"0")}`:`${n}:${f.toString().padStart(2,"0")}`};return(r,t)=>(a(),d("div",null,[e("div",ke,[c(x(_),{type:"button",variant:"outline",onClick:t[0]||(t[0]=n=>i("addBreak"))},{prefix:m(()=>t[4]||(t[4]=[e("svg",{class:"h-5 w-5",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 6v6m0 0v6m0-6h6m-6 0H6"})],-1)])),default:m(()=>[t[5]||(t[5]=b(" Add Break "))]),_:1})]),e("div",we,[t[7]||(t[7]=e("h3",{class:"text-lg font-medium text-neutral-900"},"Setlist",-1)),e("div",$e,[t[6]||(t[6]=e("svg",{class:"h-4 w-4 mr-1",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"})],-1)),b(" Total Duration: "+u(o(s.totalDuration)),1)])]),c(x(D),{"model-value":s.items,"onUpdate:modelValue":t[3]||(t[3]=n=>i("update:items",n)),"item-key":"id",handle:".drag-handle",class:"min-h-[400px] rounded-lg border-2 border-dashed border-neutral-200 p-4"},V({item:m(({element:n,index:f})=>[c(ye,{item:n,index:f,onRemove:t[1]||(t[1]=v=>i("remove",v)),onUpdateNotes:t[2]||(t[2]=v=>i("updateNotes",v))},null,8,["item","index"])]),_:2},[s.items.length===0?{name:"footer",fn:m(()=>[t[8]||(t[8]=e("div",{class:"text-center py-12"},[e("svg",{class:"mx-auto h-12 w-12 text-neutral-400",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"})]),e("h3",{class:"mt-2 text-sm font-medium text-neutral-900"},"No items in setlist"),e("p",{class:"mt-1 text-sm text-neutral-500"},"Add songs or breaks to your setlist.")],-1))]),key:"0"}:void 0]),1032,["model-value"])]))}};export{Ae as _,_e as a,De as b,qe as c};
