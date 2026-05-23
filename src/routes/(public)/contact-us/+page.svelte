<script>
	import Pageslider from "$lib/components/pageslider.svelte";
	import Title from "$lib/components/title.svelte";
	import { API_BASE_URL } from "$lib/config/base_urls";
	import { fetch_resource, post_resource } from "$lib/methods/functions";
	import { v4 } from "uuid";
    import pkg from 'notiflix';
	import { onMount } from "svelte";
	import { org_details } from "$lib/methods/methods";

    const { Notify, Confirm } = pkg;

    const URL=`${API_BASE_URL}messages.php`;

    const RESOURCE="Messages";

    let organisation;

    let name = '';
    let email = '';
    let subject = '';
    let message = '';

    let sendingMessage=false;

    let title="Contact Us";

    let subtitle="Get in touch";

    let tagline="Get in Touch With Us";


    const resetForm=()=>{
        // reset the fields 
        name = '';
        email = '';
        subject = '';
        message = '';
    }
    
    const handleSubmit=async()=> {

        let dt={
            message_id:v4(),
            name:name,
            email:email,
            subject:subject,
            message:message,
            status:"pending",
        }

        let headers={
            'Content-Type':'application/x-www-form-urlencoded'
             }

        try {

            sendingMessage=true;

            const RESPONSE=await post_resource(RESOURCE,URL,dt,headers);

            const RES=RESPONSE.data;

            if(RES.success){
                Notify.success(RES.message);

                resetForm();

            }else{
                Notify.failure(RES.message);
            }

            console.log(RES);

            sendingMessage=false;

            
        } catch (err) {
            console.log(err);
        }
       

    }

    onMount(async()=>{
        organisation=await org_details();
    })
</script>


<main>

    <div class="">
        <Pageslider {title} {tagline}/>
    </div>

    <div class="content">
        <div class="my-4">
            <Title {title} {subtitle}/>
        </div>


        <div class="contact-grid">
        <div class="contact-info">
            <div class="info-item">
                <h3>📍 Location</h3>
                <p>P.O Box {organisation?.address.postal_code}<br>{organisation?.address.constituency}, {organisation?.address.county}</p>
            </div>
            
            <div class="info-item">
                <h3>📞 Phone</h3>
                <p>
                    <a href="tel:+{organisation?.phone}">{organisation?.phone}</a>
                </p>
            </div>
            
            <div class="info-item">
                <h3><i class="ri-mail-line"></i> Email</h3>
                <p>
                    <a href="mailto:{organisation?.email}">{organisation?.email}</a>
                </p>
            </div>
            
            <div class="info-item">
                <h3><i class="ri-time-line"></i> Business Hours</h3>
                <p>Monday - Friday: 8:00 AM - 6:00 PM<br>Saturday: 9:00 AM - 4:00 PM</p>
            </div>
        </div>
        
        <div class="contact-form">
            <form onsubmit={(e)=>{
                e.preventDefault();

                handleSubmit();
            }}>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" bind:value={name} required />
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" bind:value={email} required />
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" bind:value={subject} required />
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" rows="5" bind:value={message} required></textarea>
                </div>
                
                <button type="submit" class={sendingMessage?"loading submit-btn":"submit-btn"}>Send Message</button>
            </form>
        </div>
    </div>
    </div>
</main>



<style>

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
    }
    
    @media (max-width: 768px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .contact-info {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    
    .info-item {
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(10px);
        padding: 1.5rem;
        border-radius: 10px;
    }
    
    .info-item h3 {
        color: rgb(30, 29, 29);
        margin: 0 0 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .info-item p {
        color: rgb(54, 50, 42);
        margin: 0;
        line-height: 1.6;
    }
    
    .contact-form {
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(10px);
        padding: 2rem;
        border-radius: 10px;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    label {
        display: block;
                color: rgb(30, 29, 29);

        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    input, textarea {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid rgba(255, 255, 255, 0.7);
        border-radius: 5px;
        background: rgba(255, 255, 255, 0.4);
        color: rgb(30, 29, 29);
        
        font-size: 1rem;
    }
    
    input:focus, textarea:focus {
        outline: none;
        border-color: #667eea;
        background: rgba(255, 255, 255, 0.15);
    }
    
    textarea {
        resize: vertical;
        min-height: 120px;
    }
    
    .submit-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(45deg, #41EAD4, #F71735);
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
</style>