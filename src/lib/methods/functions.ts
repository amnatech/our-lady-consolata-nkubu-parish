import axios from "axios";

export const fetch_resource: (resource: string, url: string, options: { filter?: string, order?: string, page?: number }) => Promise<{ success: boolean; message: string; data: any; }> = async (resource: string, url: string, options: { filter?: string, order?: string, page?: number } = {}): Promise<{ success: boolean; message: string; data: any; }> => {

    try {

        const response = await axios({
            url: url,
            method: "GET",

        })

        const res = response.data;

        return {
            success: res.success,
            message: res.message,
            data: res.detail
        }

    } catch (err) {
        console.log(err);
        return {
            success: false,
            message: "Error Making Network Request",
            data: err
        }
    }

}


export const post_resource: (resource: string, url: string,data:any,headers:any, options: { filter?: string, order?: string, page?: number }) => Promise<{ success: boolean; message: string; data: any; }> = async (resource: string, url: string,data:any,headers:any, options: { filter?: string, order?: string, page?: number } = {}): Promise<{ success: boolean; message: string; data: any; }> => {

    try {

        const response = await axios({
            url: url,
            method: "POST",
            data:data,
            headers:headers

        })

        const res = response.data;

        return {
            success: res.success,
            message: res.message,
            data: res.detail
        }

    } catch (err) {
        console.log(err);
        return {
            success: false,
            message: "Error Making Network Request",
            data: err
        }
    }

}