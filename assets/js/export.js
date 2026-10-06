
/*
==========================================================
 SimuRithm
 Export Module
==========================================================
*/

/*
----------------------------------------------------------
 Store Latest Analysis
----------------------------------------------------------
*/

function storeAnalysis(data){

    latestAnalysis = data;

}


/*
----------------------------------------------------------
 Export Excel
----------------------------------------------------------
*/

$("#btnExportExcel").click(function(e){

    e.preventDefault();

    if(latestAnalysis === null){

        alert("Please analyze a dataset first.");

        return;

    }

    $.ajax({

        url:"ajax/export_excel.php",

        type:"POST",

        data:{
            analysis:JSON.stringify(latestAnalysis)
        },

        xhrFields:{
            responseType:"blob"
        },

        success:function(blob){

            let link = document.createElement("a");

            link.href = window.URL.createObjectURL(blob);

            link.download = "SimuRithm_Report.xlsx";

            document.body.appendChild(link);

            link.click();

            document.body.removeChild(link);

            window.URL.revokeObjectURL(link.href);

        },

        error:function(){

            alert("Excel export failed.");

        }

    });

});


/*
----------------------------------------------------------
 Export PDF
----------------------------------------------------------
*/

$("#btnExportPDF").click(function(e){

    e.preventDefault();

    if(latestAnalysis === null){

        alert("Please analyze a dataset first.");

        return;

    }

    $.ajax({

        url:"ajax/export_pdf.php",

        type:"POST",

        data:{
            analysis:JSON.stringify(latestAnalysis)
        },

        xhrFields:{
            responseType:"blob"
        },

        success:function(blob){

            let link = document.createElement("a");

            link.href = window.URL.createObjectURL(blob);

            link.download = "SimuRithm_Report.pdf";

            document.body.appendChild(link);

            link.click();

            document.body.removeChild(link);

            window.URL.revokeObjectURL(link.href);

        },

        error:function(){

            alert("PDF export failed.");

        }

    });

});