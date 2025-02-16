import{C as g,c as o,o as a,a as n,u as s,m as _,w as i,b as e,d as y,e as h,h as c,F as f,j as k,t as p,i as w,n as B,q as C,P as V,f as j}from"./app-CG_txIVR.js";import{_ as N}from"./AuthenticatedLayout-DJIiH2wS.js";import{_ as x}from"./Button-Kx0rHSGw.js";import{_ as M}from"./Input-DmtGEFtB.js";import{_ as S}from"./Card-BOaGNwsG.js";import{_ as U}from"./Alert-DDOMzzWl.js";const $={class:"md:flex md:items-center md:justify-between"},q={class:"min-w-0 flex-1"},E={class:"text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight"},P={class:"list-disc list-inside"},D={class:"mt-1"},F={class:"mt-1"},G={key:0,class:"mb-4"},I=["src","alt"],L={class:"flex justify-center rounded-lg border border-dashed border-neutral-300 px-6 py-10"},z={class:"text-center"},O={class:"mt-4 flex text-sm text-neutral-600"},T={for:"cover-image",class:"relative cursor-pointer rounded-md font-medium text-primary-600 hover:text-primary-500"},H={key:0,class:"mt-1 text-sm text-error-500"},J={class:"flex items-center justify-end space-x-4"},A={key:0},K={key:1},ee={__name:"Edit",props:{band:{type:Object,required:!0}},setup(l){const m=l,r=g({name:m.band.name,description:m.band.description,cover_image:null}),b=()=>{r.patch(route("bands.update",m.band.id),{preserveScroll:!0})},v=u=>{const t=u.target.files[0];t&&(r.cover_image=t)};return(u,t)=>(a(),o(f,null,[n(s(_),{title:"Edit Band"}),n(N,null,{header:i(()=>[e("div",$,[e("div",q,[e("h2",E," Edit "+p(l.band.name),1),t[2]||(t[2]=e("p",{class:"mt-1 text-sm text-neutral-500"}," Update your band's information and settings ",-1))])])]),default:i(()=>[n(s(S),{class:"max-w-2xl"},{default:i(()=>[e("form",{onSubmit:y(b,["prevent"]),class:"space-y-6 p-6"},[Object.keys(s(r).errors).length>0?(a(),h(s(U),{key:0,type:"error"},{default:i(()=>[e("ul",P,[(a(!0),o(f,null,k(s(r).errors,d=>(a(),o("li",{key:d},p(d),1))),128))])]),_:1})):c("",!0),e("div",null,[n(s(M),{modelValue:s(r).name,"onUpdate:modelValue":t[0]||(t[0]=d=>s(r).name=d),type:"text",label:"Band Name",error:s(r).errors.name,required:""},null,8,["modelValue","error"]),t[3]||(t[3]=e("p",{class:"mt-1 text-sm text-neutral-500"}," Choose a unique name for your band ",-1))]),e("div",null,[t[4]||(t[4]=e("label",{class:"block text-sm font-medium text-neutral-700"}," Description ",-1)),e("div",D,[w(e("textarea",{"onUpdate:modelValue":t[1]||(t[1]=d=>s(r).description=d),rows:"4",class:B(["block w-full rounded-lg shadow-sm transition-colors duration-200","border-neutral-300 focus:border-primary-500 focus:ring-primary-500",{"border-error-500 focus:border-error-500 focus:ring-error-500":s(r).errors.description}])},null,2),[[C,s(r).description]])]),t[5]||(t[5]=e("p",{class:"mt-1 text-sm text-neutral-500"}," Brief description of your band and its style ",-1))]),e("div",null,[t[10]||(t[10]=e("label",{class:"block text-sm font-medium text-neutral-700"}," Cover Image ",-1)),e("div",F,[l.band.cover_image?(a(),o("div",G,[e("img",{src:l.band.cover_image,alt:l.band.name,class:"h-32 w-32 object-cover rounded-lg"},null,8,I)])):c("",!0),e("div",L,[e("div",z,[t[8]||(t[8]=e("svg",{class:"mx-auto h-12 w-12 text-neutral-400",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"})],-1)),e("div",O,[e("label",T,[t[6]||(t[6]=e("span",null,"Upload a new image",-1)),e("input",{id:"cover-image",type:"file",class:"sr-only",accept:"image/*",onChange:v},null,32)]),t[7]||(t[7]=e("p",{class:"pl-1"},"or drag and drop",-1))]),t[9]||(t[9]=e("p",{class:"text-xs text-neutral-500"},"PNG, JPG, GIF up to 10MB",-1))])])]),s(r).errors.cover_image?(a(),o("p",H,p(s(r).errors.cover_image),1)):c("",!0)]),e("div",J,[n(s(V),{href:u.route("bands.show",l.band.id)},{default:i(()=>[n(s(x),{type:"button",variant:"outline"},{default:i(()=>t[11]||(t[11]=[j(" Cancel ")])),_:1})]),_:1},8,["href"]),n(s(x),{type:"submit",variant:"primary",disabled:s(r).processing},{default:i(()=>[s(r).processing?(a(),o("span",A,"Saving changes...")):(a(),o("span",K,"Save changes"))]),_:1},8,["disabled"])])],32)]),_:1})]),_:1})],64))}};export{ee as default};
