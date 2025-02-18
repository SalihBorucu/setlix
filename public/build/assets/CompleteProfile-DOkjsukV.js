import{C as d,c as l,o as m,a as r,u as e,m as p,w as n,b as t,d as f,F as c}from"./app-RtlUSPMi.js";import{_}from"./GuestLayout-CVAWa0cz.js";import{_ as w}from"./Button-D2tZ0rOX.js";import{_ as i}from"./Input-ByC62vXA.js";import{_ as x}from"./Card-ucR7er3D.js";const y={class:"flex items-center justify-end"},b={key:0},V={key:1},q={__name:"CompleteProfile",setup(C){const o=d({name:"",password:"",password_confirmation:""}),u=()=>{o.post(route("profile.complete.update"),{onSuccess:()=>{o.reset()}})};return(g,s)=>(m(),l(c,null,[r(e(p),{title:"Complete Profile Setup"}),r(_,null,{default:n(()=>[r(e(x),{class:"p-6 w-full max-w-xl mx-auto"},{default:n(()=>[s[4]||(s[4]=t("div",{class:"text-center mb-8"},[t("h2",{class:"text-2xl font-bold text-gray-900"},"Welcome to Setlix!"),t("p",{class:"mt-2 text-sm text-gray-600"}," Please complete your profile to continue ")],-1)),t("form",{onSubmit:f(u,["prevent"]),class:"space-y-6"},[t("div",null,[r(e(i),{modelValue:e(o).name,"onUpdate:modelValue":s[0]||(s[0]=a=>e(o).name=a),type:"text",label:"Full Name",error:e(o).errors.name,required:"",autofocus:""},null,8,["modelValue","error"]),s[3]||(s[3]=t("p",{class:"mt-1 text-sm text-gray-500"},"This is how you'll appear to other band members",-1))]),t("div",null,[r(e(i),{modelValue:e(o).password,"onUpdate:modelValue":s[1]||(s[1]=a=>e(o).password=a),type:"password",label:"Choose a Password",error:e(o).errors.password,required:""},null,8,["modelValue","error"])]),t("div",null,[r(e(i),{modelValue:e(o).password_confirmation,"onUpdate:modelValue":s[2]||(s[2]=a=>e(o).password_confirmation=a),type:"password",label:"Confirm Password",required:""},null,8,["modelValue"])]),t("div",y,[r(e(w),{type:"submit",variant:"primary",class:"w-full",disabled:e(o).processing},{default:n(()=>[e(o).processing?(m(),l("span",b,"Setting up your account...")):(m(),l("span",V,"Complete Setup & Continue"))]),_:1},8,["disabled"])])],32)]),_:1})]),_:1})],64))}};export{q as default};
