import{C as c,c as i,o as l,a,b as o,u as e,m as y,f as n,w as m,P as _,e as w,h as x,F as p,j as g,t as V}from"./app-CbefnPLH.js";import{_ as b}from"./DSTooltip.vue_vue_type_style_index_0_lang-8fPiiRMW.js";import{_ as d}from"./Input-DOwheC4v.js";import{_ as v}from"./Form-Qua4jrWv.js";import{_ as k}from"./Alert-DjKDwrjq.js";import{A as h}from"./ApplicationLogo-Wl5igHkP.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";const C={class:"min-h-screen bg-neutral-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8"},B={class:"max-w-md w-full space-y-8"},P={class:"text-center"},S={class:"mt-2 text-sm text-neutral-600"},j={class:"space-y-6"},q={class:"list-disc list-inside"},F={key:0},N={key:1},D={__name:"Register",setup(U){const r=c({name:"",email:"",password:"",password_confirmation:""}),u=()=>{r.post(route("register"),{onFinish:()=>r.reset("password","password_confirmation")})};return(f,t)=>(l(),i(p,null,[a(e(y),{title:"Register"}),o("main",C,[o("div",B,[o("div",P,[a(h),t[6]||(t[6]=o("h2",{class:"mt-6 text-3xl font-display font-bold text-neutral-900"}," Start your journey ",-1)),o("p",S,[t[5]||(t[5]=n(" Already have an account? ")),a(e(_),{href:f.route("login"),class:"font-medium text-primary-500 hover:text-primary-600"},{default:m(()=>t[4]||(t[4]=[n(" Sign in ")])),_:1},8,["href"])])]),a(e(v),{onSubmit:u,class:"mt-8"},{default:m(()=>[o("div",j,[Object.keys(e(r).errors).length>0?(l(),w(e(k),{key:0,type:"error"},{default:m(()=>[o("ul",q,[(l(!0),i(p,null,g(e(r).errors,s=>(l(),i("li",{key:s},V(s),1))),128))])]),_:1})):x("",!0),a(e(d),{type:"text",label:"Full name",modelValue:e(r).name,"onUpdate:modelValue":t[0]||(t[0]=s=>e(r).name=s),error:e(r).errors.name,required:"",autocomplete:"name"},null,8,["modelValue","error"]),a(e(d),{type:"email",label:"Email address",modelValue:e(r).email,"onUpdate:modelValue":t[1]||(t[1]=s=>e(r).email=s),error:e(r).errors.email,required:"",autocomplete:"email"},null,8,["modelValue","error"]),a(e(d),{type:"password",label:"Password",modelValue:e(r).password,"onUpdate:modelValue":t[2]||(t[2]=s=>e(r).password=s),error:e(r).errors.password,required:"",autocomplete:"new-password"},null,8,["modelValue","error"]),a(e(d),{type:"password",label:"Confirm password",modelValue:e(r).password_confirmation,"onUpdate:modelValue":t[3]||(t[3]=s=>e(r).password_confirmation=s),error:e(r).errors.password_confirmation,required:"",autocomplete:"new-password"},null,8,["modelValue","error"]),t[7]||(t[7]=o("div",{class:"text-sm text-neutral-600"},[n(" By registering, you agree to our "),o("a",{href:"#",class:"font-medium text-primary-500 hover:text-primary-600"},"Terms of Service"),n(" and "),o("a",{href:"#",class:"font-medium text-primary-500 hover:text-primary-600"},"Privacy Policy")],-1)),a(e(b),{type:"submit",variant:"primary",size:"lg",class:"w-full",disabled:e(r).processing},{default:m(()=>[e(r).processing?(l(),i("span",F,"Creating account...")):(l(),i("span",N,"Create account"))]),_:1},8,["disabled"])])]),_:1})])])],64))}};export{D as default};
