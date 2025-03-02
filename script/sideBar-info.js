import { infoOfStudent, fetchDataInfo } from "../data/profile-info-data.js";

class PersonalData {
    constructor(allInfo){
        this.allInfo = allInfo;
    }

    distributeStudentSideBarInfo(){
        document.querySelector(".first-span").textContent = `${this.allInfo.first_name} ${this.allInfo.last_name}`;
        document.querySelector(".second-span").textContent = `#${this.allInfo.student_id}`;
        document.querySelector("#preview2").src = this.allInfo.image_url || "image/user-default-profile.jpg";
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    async function loadStudentSideBarInfo(){
        try {
            await fetchDataInfo();  
        } 
        catch (error) {
            console.log(error);
        }

        const personalInfo = new PersonalData(infoOfStudent);
        personalInfo.distributeStudentSideBarInfo();
    }

    loadStudentSideBarInfo();
});