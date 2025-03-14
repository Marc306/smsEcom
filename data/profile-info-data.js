// export async function fetchDataInfo(){
//     try {
//         const response = await fetch("http://localhost/smsEcommerce/php/profileInfo.php");
//         if (!response.ok) {
//             throw new Error(`HTTP error! Status: ${response.status}`);
//         }
//         return await response.json();
//     } catch (error) {
//         console.error(`Unexpected ${error}. Please try again later.`);
//         return null;
//     }
// }


export let infoOfStudent;
export function fetchDataInfo(){
    const dataInfo = fetch("https://ecommerce.schoolmanagementsystem2.com/php/profileInfo.php").then((response) =>{
        return response.json();
    }).then((dataResponse) =>{
        infoOfStudent = dataResponse;
        console.log(dataResponse);
    }).catch((error) => {
        console.log(`Unexpected ${error}. Please try again later.`);
    });
    
    return dataInfo;
}