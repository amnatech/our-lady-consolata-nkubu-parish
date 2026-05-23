import { goto } from "$app/navigation";
import { browser } from '$app/environment';
import dayjs from "dayjs";
import pkg from 'notiflix';
import { API_BASE_URL } from "$lib/config/base_urls";
import { fetch_resource } from "./functions";
const { Notify, Confirm } = pkg;


export const format_date = (d, format) => {

    // format desctribe how to format the date 
    if (!format) {

        if (d.length > 12) {
            format = "MMM DD, YYYY H:m:s"
        } else {
            format = "'MMM DD, YYYY";

        }
    }

    // if date includes time
    if (d.length > 12) {
        return dayjs(d).format(format);

    } else {
        return dayjs(d).format(format);

    }
}



export const add_commas = (number, dp) => {
    return number.toLocaleString("en-US", {
        minimumFractionDigits: dp,
        maximumFractionDigits: dp,
    });
}

//current liu
export const current_liu = () => {

    let liu = {};

    if (browser) {

        let clia = window.sessionStorage.wekebio_liu;

        if (!clia) {
            // goto('/user/login');

            return;
        }

        liu = JSON.parse(clia);
    }

    return liu;
}
//logout
export const logout = () => {
    // session id to logout
    Confirm.show(
        "Logout",
        `Logout from this device?`,
        "Yes",
        "No",
        () => {
            let ck = "wekebio_liu";

            window.sessionStorage.removeItem('wekebio_liu');

            goto("/", { replaceState: true });

        },
        () => {
            //    do nothing
        },
        {}
    );
}


export const slugify = (str) => {
    str = str.replace(/^\s+|\s+$/g, ''); // trim leading/trailing white space
    str = str.toLowerCase(); // convert string to lowercase
    str = str.replace(/[^a-z0-9 -]/g, '') // remove any non-alphanumeric characters
        .replace(/\s+/g, '-') // replace spaces with hyphens
        .replace(/-+/g, '-'); // remove consecutive hyphens
    return str;
}


export const calculate_percentage = (value, total) => {
    let disc = add_commas((value / total) * 100, 2)

    return disc;
}


export const create_pages = (size) => {
    return [...Array(size).keys()];
}

export const get_current_page = (url) => {

    let url_arr = url.split('=');

    if (url_arr[1]) {
        return url_arr[1];
    } else {
        return 1;
    }
}


export const make_pages=(items,page_size)=>{

    let total_pages=Math.ceil(items / page_size);

     return [...Array(total_pages).keys()];
}


export const update_page=(items,page,page_size)=>{

    let offset=(page-1)*page_size;

    return items.slice(offset,offset+page_size);
}

export const org_details=async()=>{
        const ORG_URL=`${API_BASE_URL}org.php`;

        const RESOURCE="Messages";

        try {
            const RESPONSE=await fetch_resource(RESOURCE,ORG_URL);

            const RES=RESPONSE.data;

            return RES;
        } catch (err) {
            console.log(err)
        }
    }


export  const get_activities=async()=>{
        const URL=`${API_BASE_URL}activities.php`;

        const RESOURCE="Activities";

        try {

            const RESPONSE=await fetch_resource(RESOURCE,URL);

            return RESPONSE.data;
            
        } catch (err) {
            console.log(err)
        }
}


export  const get_groups=async()=>{
        const URL=`${API_BASE_URL}groups.php`;

        const RESOURCE="Groups";

        try {

            const RESPONSE=await fetch_resource(RESOURCE,URL);

            return RESPONSE.data;
            
        } catch (err) {
            console.log(err)
        }
}

export  const get_users=async()=>{
        const URL=`${API_BASE_URL}users.php`;

        const RESOURCE="Users";

        try {

            const RESPONSE=await fetch_resource(RESOURCE,URL);

            return RESPONSE.data;
            
        } catch (err) {
            console.log(err)
        }
}


export  const get_houses=async()=>{
        const URL=`${API_BASE_URL}houses.php`;

        const RESOURCE="Activities";

        try {

            const RESPONSE=await fetch_resource(RESOURCE,URL);

            return RESPONSE.data;
            
        } catch (err) {
            console.log(err)
        }
}

export  const get_projects=async()=>{
        const URL=`${API_BASE_URL}projects.php`;

        const RESOURCE="Projects";

        try {

            const RESPONSE=await fetch_resource(RESOURCE,URL);

            return RESPONSE.data;
            
        } catch (err) {
            console.log(err)
        }
}


export  const get_news_and_events=async()=>{
        const URL=`${API_BASE_URL}news-and-events.php`;

        const RESOURCE="Projects";

        try {

            const RESPONSE=await fetch_resource(RESOURCE,URL);

            return RESPONSE.data;
            
        } catch (err) {
            console.log(err)
        }
}


export  const get_messages=async()=>{
        const URL=`${API_BASE_URL}messages.php`;

        const RESOURCE="Messages";

        try {

            const RESPONSE=await fetch_resource(RESOURCE,URL);

            return RESPONSE.data;
            
        } catch (err) {
            console.log(err)
        }
}


