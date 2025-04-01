import{r as p,C as x,e as v,o as i,w as a,b as s,a as l,u as t,f,B as k,c as _}from"./app-Dc_Fn3fE.js";import{_ as c}from"./DSTooltip.vue_vue_type_style_index_0_lang-B9_KI25U.js";import{_ as b}from"./Input-yf_LBPr5.js";import{_ as g}from"./Card-D-rXPIZf.js";import{_ as C}from"./Modal-XcdwLtnc.js";/* empty css            */import"./transition-Bm63RUDT.js";const D={class:"p-6 space-y-6"},V={class:"flex justify-start"},B={class:"p-6"},U={class:"mt-6"},h={class:"mt-6 flex justify-end space-x-3"},A={key:0},$={key:1},S={__name:"DeleteUserForm",setup(N){const n=p(!1),d=p(null),o=x({password:""}),y=()=>{n.value=!0,setTimeout(()=>{var r;return(r=d.value)==null?void 0:r.focus()},250)},m=()=>{o.delete(route("profile.destroy"),{preserveScroll:!0,onSuccess:()=>u(),onError:()=>{var r;return(r=d.value)==null?void 0:r.focus()},onFinish:()=>o.reset()})},u=()=>{n.value=!1,o.reset()};return(r,e)=>(i(),v(t(g),{class:"max-w-2xl"},{default:a(()=>[s("div",D,[e[5]||(e[5]=s("header",null,[s("h2",{class:"text-2xl font-bold text-neutral-900"}," Delete Account "),s("p",{class:"mt-1 text-sm text-neutral-500"}," Once your account is deleted, all of its resources and data will be permanently deleted. ")],-1)),s("div",V,[l(t(c),{variant:"danger",onClick:y},{default:a(()=>e[1]||(e[1]=[f(" Delete Account ")])),_:1})]),l(t(C),{show:n.value,onClose:u},{default:a(()=>[s("div",B,[e[3]||(e[3]=s("h2",{class:"text-lg font-medium text-neutral-900"}," Are you sure you want to delete your account? ",-1)),e[4]||(e[4]=s("p",{class:"mt-1 text-sm text-neutral-600"}," Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account. ",-1)),s("div",U,[l(t(b),{modelValue:t(o).password,"onUpdate:modelValue":e[0]||(e[0]=w=>t(o).password=w),type:"password",label:"Password",ref_key:"passwordInput",ref:d,error:t(o).errors.password,required:"",onKeyup:k(m,["enter"])},null,8,["modelValue","error"])]),s("div",h,[l(t(c),{variant:"outline",onClick:u},{default:a(()=>e[2]||(e[2]=[f(" Cancel ")])),_:1}),l(t(c),{variant:"danger",disabled:t(o).processing,onClick:m},{default:a(()=>[t(o).processing?(i(),_("span",A,"Deleting...")):(i(),_("span",$,"Delete Account"))]),_:1},8,["disabled"])])])]),_:1},8,["show"])])]),_:1}))}};export{S as default};
