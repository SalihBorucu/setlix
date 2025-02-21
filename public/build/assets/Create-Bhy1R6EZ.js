import{C as B,r as p,l as I,c as u,o as l,a as r,u as a,m as N,w as i,b as c,d as q,e as j,h as A,F as y,j as V,t as F,P as L,f as U}from"./app-D8OPSLji.js";import{_ as D}from"./AuthenticatedLayout-DTlQNGiu.js";import{_ as b}from"./Button-D80aJfgU.js";import{_ as E}from"./Alert-132oDIUR.js";import{_ as M,a as O,b as P,c as Q,d as T}from"./BreakModal-oUFlHo5T.js";import"./Input-CpHXBpmC.js";import"./Card-BGvY5qee.js";const R={class:"max-w-7xl"},z={class:"list-disc list-inside"},G={class:"grid grid-cols-1 lg:grid-cols-2 gap-6"},H={class:"flex items-center justify-end space-x-4"},J={key:0},K={key:1},oe={__name:"Create",props:{band:{type:Object,required:!0},songs:{type:Array,required:!0}},setup(v){const f=v,n=B({band_id:f.band.id,name:"",description:"",items:[],total_duration:0}),d=p([...f.songs]),o=p([]),_=p(!1),m=p(""),h=I(()=>{if(!m.value)return d.value;const t=m.value.toLowerCase();return d.value.filter(e=>e.name.toLowerCase().includes(t))}),g=()=>{n.total_duration=o.value.reduce((t,e)=>t+(e.duration_seconds||0),0)},C=t=>{o.value.push({id:`break-${Date.now()}`,...t,pivot:{notes:t.notes,order:o.value.length}}),g()},k=t=>{const e={...t,type:"song",pivot:{notes:"",order:o.value.length}};o.value.push(e);const s=d.value.findIndex(x=>x.id===t.id);s!==-1&&d.value.splice(s,1),g()},S=t=>{const e=o.value[t];if(e.type==="song"){const s={...e};delete s.pivot,d.value.push(s)}o.value.splice(t,1),g()},$=({index:t,notes:e})=>{o.value[t]&&(o.value[t].pivot.notes=e)},w=()=>{n.items=o.value.map(t=>{const e={type:t.type,duration_seconds:t.duration_seconds,notes:t.pivot.notes};return t.type==="song"?(e.song_id=t.id,e.title=null):(e.song_id=null,e.title=t.title),e}),console.log("Submitting items:",n.items),n.post(route("setlists.store",f.band.id),{preserveScroll:!0,onError:t=>{console.error("Form errors:",t)}})};return(t,e)=>(l(),u(y,null,[r(a(N),{title:"Create Setlist"}),r(D,null,{header:i(()=>[r(T,{band:v.band},null,8,["band"])]),default:i(()=>[c("div",R,[c("form",{onSubmit:q(w,["prevent"]),class:"space-y-6"},[Object.keys(a(n).errors).length>0?(l(),j(a(E),{key:0,type:"error"},{default:i(()=>[c("ul",z,[(l(!0),u(y,null,V(a(n).errors,s=>(l(),u("li",{key:s},F(s),1))),128))])]),_:1})):A("",!0),r(M,{form:a(n)},null,8,["form"]),c("div",G,[r(O,{songs:h.value,"search-query":m.value,"onUpdate:searchQuery":e[0]||(e[0]=s=>m.value=s),onAddSong:k},null,8,["songs","search-query"]),r(P,{items:o.value,"onUpdate:items":e[1]||(e[1]=s=>o.value=s),"total-duration":a(n).total_duration,onRemove:S,onUpdateNotes:$,onAddBreak:e[2]||(e[2]=s=>_.value=!0)},null,8,["items","total-duration"])]),c("div",H,[r(a(L),{href:t.route("setlists.index",v.band.id)},{default:i(()=>[r(a(b),{type:"button",variant:"outline"},{default:i(()=>e[4]||(e[4]=[U(" Cancel ")])),_:1})]),_:1},8,["href"]),r(a(b),{type:"submit",variant:"primary",disabled:a(n).processing||o.value.length===0},{default:i(()=>[a(n).processing?(l(),u("span",J,"Creating...")):(l(),u("span",K,"Create Setlist"))]),_:1},8,["disabled"])])],32)]),r(Q,{"is-open":_.value,onClose:e[3]||(e[3]=s=>_.value=!1),onCreate:C},null,8,["is-open"])]),_:1})],64))}};export{oe as default};
