import{C as q,r as f,q as B,x as I,c as p,o as i,a as n,u as o,m as N,w as d,b as m,d as j,e as A,h as E,F as y,j as V,t as F,P as L,f as M}from"./app-By7LNvvF.js";import{_ as O}from"./AuthenticatedLayout-Bnlojeeg.js";import{_ as b}from"./DSTooltip.vue_vue_type_style_index_0_lang-DhlZjsMt.js";import{_ as U}from"./Alert-BZb8NcnJ.js";import{_ as D,a as P,b as Q,c as T}from"./SetlistItemsList-DybyK9A8.js";import{_ as R}from"./BreakModal-Dt3HaUUG.js";import"./Modal-C-Af_cyN.js";import"./transition-CiZYRrp6.js";import"./Input-eS0M8yT6.js";import"./Card-BxvmkG30.js";import"./DSDurationInput-7Wvo9swi.js";const z={class:"max-w-7xl"},G={class:"list-disc list-inside"},H={class:"grid grid-cols-1 lg:grid-cols-2 gap-6"},J={class:"flex items-center justify-end space-x-4"},K={key:0},W={key:1},ie={__name:"Edit",props:{band:{type:Object,required:!0},setlist:{type:Object,required:!0},availableSongs:{type:Array,required:!0}},setup(u){const r=u,l=q({band_id:r.band.id,name:r.setlist.name,description:r.setlist.description,target_duration:"",target_duration_seconds:r.setlist.target_duration,items:[],total_duration:r.setlist.total_duration}),c=f([...r.availableSongs]),s=f([]),_=f(!1);B(()=>{s.value=r.setlist.items.map(e=>({...e,id:e.type==="break"?`break-${e.id}`:e.song.id,name:e.type==="break"?e.title:e.song.name,pivot:{notes:e.notes,order:e.order}}))});const v=f(""),h=I(()=>{if(!v.value)return c.value;const e=v.value.toLowerCase();return c.value.filter(t=>t.name.toLowerCase().includes(e))}),g=()=>{l.total_duration=s.value.reduce((e,t)=>e+(t.duration_seconds||0),0)},k=e=>{s.value.push({id:`break-${Date.now()}`,...e,pivot:{notes:e.notes,order:s.value.length}}),g()},S=e=>{const t={...e,type:"song",pivot:{notes:"",order:s.value.length}};s.value.push(t);const a=c.value.findIndex(x=>x.id===e.id);a!==-1&&c.value.splice(a,1),g()},$=e=>{const t=s.value[e];if(t.type==="song"){const a={...t};delete a.pivot,c.value.push(a)}s.value.splice(e,1),g()},w=({index:e,notes:t})=>{s.value[e]&&(s.value[e].pivot.notes=t)},C=()=>{l.items=s.value.map(e=>{const t={type:e.type,duration_seconds:e.duration_seconds,notes:e.pivot.notes};return e.type==="song"?(t.song_id=e.id,t.title=null):(t.song_id=null,t.title=e.title||e.name),t}),l.put(route("setlists.update",[r.band.id,r.setlist.id]),{preserveScroll:!0,onError:e=>{console.error("Form errors:",e)}})};return(e,t)=>(i(),p(y,null,[n(o(N),{title:"Edit Setlist"}),n(O,null,{header:d(()=>[n(T,{band:u.band,setlist:u.setlist},null,8,["band","setlist"])]),default:d(()=>[m("div",z,[m("form",{onSubmit:j(C,["prevent"]),class:"space-y-6"},[Object.keys(o(l).errors).length>0?(i(),A(o(U),{key:0,type:"error"},{default:d(()=>[m("ul",G,[(i(!0),p(y,null,V(o(l).errors,a=>(i(),p("li",{key:a},F(a),1))),128))])]),_:1})):E("",!0),n(D,{form:o(l)},null,8,["form"]),m("div",H,[n(P,{songs:h.value,"search-query":v.value,"onUpdate:searchQuery":t[0]||(t[0]=a=>v.value=a),onAddSong:S},null,8,["songs","search-query"]),n(Q,{items:s.value,"onUpdate:items":t[1]||(t[1]=a=>s.value=a),"total-duration":o(l).total_duration,onRemove:$,onUpdateNotes:w,onAddBreak:t[2]||(t[2]=a=>_.value=!0)},null,8,["items","total-duration"])]),m("div",J,[n(o(L),{href:e.route("setlists.show",[u.band.id,u.setlist.id])},{default:d(()=>[n(o(b),{type:"button",variant:"outline"},{default:d(()=>t[4]||(t[4]=[M(" Cancel ")])),_:1})]),_:1},8,["href"]),n(o(b),{type:"submit",variant:"primary",disabled:o(l).processing||s.value.length===0},{default:d(()=>[o(l).processing?(i(),p("span",K,"Saving...")):(i(),p("span",W,"Save Changes"))]),_:1},8,["disabled"])])],32)]),n(R,{"is-open":_.value,onClose:t[3]||(t[3]=a=>_.value=!1),onCreate:k},null,8,["is-open"])]),_:1})],64))}};export{ie as default};
