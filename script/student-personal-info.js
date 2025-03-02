import { infoOfStudent, fetchDataInfo } from "../data/profile-info-data.js";

class PersonalData {
    constructor(allInfo){
        this.allInfo = allInfo;
    }

    distributeStudentInfo(){
        document.querySelector("#preview1").src = this.allInfo.image_url || "image/user-default-profile.jpg";

        // Personal Information
        document.querySelector(".first-name-info").textContent = this.allInfo.first_name;
        document.querySelector(".last-name-info").textContent = this.allInfo.last_name;
        document.querySelector(".email").textContent = this.allInfo.email;
        document.querySelector(".number").textContent = this.allInfo.contact_number;
        document.querySelector(".birth").textContent = this.allInfo.birthday;
        document.querySelector(".user-names").textContent = `${this.allInfo.first_name} ${this.allInfo.last_name}`;
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    async function loadStudentInfo(){
        try {
            await fetchDataInfo();  
        } 
        catch (error) {
            console.log(error);
        }
        
        const personalInfo = new PersonalData(infoOfStudent);
        personalInfo.distributeStudentInfo();
    }

    loadStudentInfo();
});

