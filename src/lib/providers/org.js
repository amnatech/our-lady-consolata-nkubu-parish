const VALUES=[
    {
        title:"Collaboration",
        description:`We recognize the power of partnerships,coorperations and collective effort to pull resources for more effective and sustainable outcomes.`,
        icon:'<i class="ri-hand-coin-line"></i>'
    },
    {
        title:"Inclusiveness",
        description:`We exist to remove barriers, promoting equal opportunities so that we can create a fair,socially right and inclusive society.`, 
        icon:'<i class="ri-apps-2-add-line"></i>'
    },
    {
        title:"Integrity",
        description:`We are honest,credible and committed to adhering to high moral and professional standard.`, 
        icon:'<i class="ri-shield-check-line"></i>'
    },
    {
        title:"Equity",
        description:`We strive for fairness and justice to ensure everyone has access to opportunities,resources and benefit according to their specific needs or circumstances.`, 
        icon:'<i class="ri-equal-line"></i>'
    }
    ]

const MISSION="We are dedicated to creating amazing nature experiences using modern technologies to protect our ecosystems. Our team is passionate about building fast, clean and sustainable practices to ensure ensure biodiversity preservation";

const VISION="Eum veritatis quam enim in reiciendis pariatur? Natus rerum accusamus culpa laborum aspernatur ipsam voluptates sint non dignissimos?";

const ORG = {
    name: "Our Lady Consolata Nkubu Catholic Parish",
    phone: "254700000000",
    alt_phone:"",
    email: "info@ourladyconsolata.or.ke",
    alt_email:"",
    website:"www.https://ourladyconsolata.org",
    tagline:"Small Change, Big Future",
    description:"Dedicated to creating innovative solutions for modern challenges.",
    reg_no:"DSD/37/209/02/166735",
    address:{
            postal_code: "148-60200",
            county:"Meru",
            subcounty:"South Imenti",
            constituency:"Imenti South",
            division:"Nkubu",
            location:"Nkubu"

    },
    detail:{
        introduction:`West Kenya Biodiversity is a community based organization (CBO) which was formed in 2005 and officially registered with the Ministry of Gender,Culture and Social Services in 1995.
            It is located approximately 20 Kilometers from Kakamega town on the Kakamega-Shinyalu Road, inside Kakamega Forest, within isecheno Forest Station of Kenya Forest Service. It has five sites in the forset, each with its special projects. The sites are Kibiri, Kisere, Bunyangu and Isecheno.`,
        who_we_are:`We are an organisation centered on fastening gender equity, empowering the youth and building strongand inclusive communities. At the core of our mission is the beliefs in the equal rights and opportunities of all genders. We strive to create a society with empowered persons by eliminating any discrimination. We believe that young people are the agents of change and hold immense potential to transform societies.`,
        team:"We have a diverse team of enviromentalists, volunteers, and commnunity members working together to deliver exceptional results for our environmental conservation efforts.",
        goal:"Our goal is to",
        mission:MISSION,
        vision:VISION,
        values:VALUES
    }
}

console.log(JSON.stringify(ORG));

export default ORG;