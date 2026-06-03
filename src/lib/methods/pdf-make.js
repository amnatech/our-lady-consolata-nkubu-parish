import pdfMake from "pdfmake/build/pdfmake";
import "pdfmake/build/vfs_fonts";
import logo from "$lib/config/new_logo";
import { API_BASE_URL } from "$lib/config/base_urls";

const { async } = pdfMake;

pdfMake.fonts = {
    Liberation: {
        normal: `${API_BASE_URL}assets/fonts/Liberation_serif/liberation-serif.regular.ttf`,
        bold: `${API_BASE_URL}assets/fonts/Liberation_serif/liberation-serif.bold.ttf`,
        italics:
            `${API_BASE_URL}assets/fonts/Liberation_serif/liberation-serif.italic.ttf`,
        bolditalics:
            `${API_BASE_URL}assets/fonts/Liberation_serif/liberation-serif.bold-italic.ttf`,
    },
    Fontello: {
        normal: `${API_BASE_URL}assets/fonts/Liberation_serif/fontello.ttf`,
        bold: `${API_BASE_URL}assets/fonts/Liberation_serif/fontello.ttf`,
        italics:
            `${API_BASE_URL}assets/fonts/Liberation_serif/fontello.ttf`,
        bolditalics:
            `${API_BASE_URL}assets/fonts/Liberation_serif/fontello.ttf`,
    },
};


const create_filter = (filters) => {

    let filter_text = "";

    filters.forEach(f => {
        filter_text = `${filter_text} ${f.name} ${f.value ? f.value : ""} | `
    });

    return filter_text;
}


export const generate_pdf_body = (columnTitles, columnWidths, rows) => {
    console.log(rows);
    let pdfBody = [];

    let tableBody = [];

    tableBody.push(columnTitles);

    rows.forEach((row, i) => {
        tableBody.push(row);
    });

    pdfBody.push({
        margin: [15, 5, 15, 15],
        layout: "lightHorizontalLines",
        table: {
            widths: columnWidths,
            headerRows: 1,
            body: tableBody,
        },
    });

    return pdfBody;
};

export const generate_pdf = (title, documentNumber, filters, columnTitles, columnWidths, rows, companyDetails) => {
    var docDefinition = {
        pageMargins: [0, 5, 5, 5],

        footer: function (currentPage, pageCount) {
            return [
                {
                    text: currentPage.toString() + " of " + pageCount,
                    style: "footerText",
                },
            ];
        },
        header: function (currentPage, pageCount, pageSize) {
            // you can apply any logic and return any valid pdfmake element

            return [
                {
                    text: "INVEMAS",
                    alignment: currentPage % 2 ? "left" : "right",
                    style: "headerText",
                },
                {
                    image: "" + logo + "",
                    width: 2000,
                    style: "logo",
                },
            ];
        },
        watermark: {
            text: companyDetails.watermark,
            color: "blue",
            opacity: 0.05,
            bold: false,
            italics: false,
            fontSize: 70,
        },
        content: [
            // logo
            {
                border: [false, false, false, false],
                image: "" + logo + "",
                width: 250,
                style: "logo",
                outerHeight: 300,
            },

            {
                style: "topCompanyName",
                text: companyDetails.name,
            },

            {
                style: "topBarTable",
                margin: [15, 15, 15, 0],
                table: {
                    widths: ["100%"],
                    body: [
                        [
                            {
                                text:
                                    "P.o. Box " +
                                    companyDetails.address.postal_code +" ,"+ companyDetails.address.location  +
                                    " " +
                                    " | " +
                                    " " +
                                    companyDetails.phone +
                                    " " +
                                    " | " +
                                    " " +
                                    companyDetails.email +
                                    " " +
                                    " | " +
                                    " " +
                                    companyDetails.address.county +", Kenya" +
                                    " " +
                                    " | " +
                                    " " +
                                    companyDetails.website,
                                border: [false, false, false, true],
                            },
                        ],
                    ],
                },
            },

            {
                style: "headerTable",
                margin: [15, 5, 15, 15],
                table: {
                    widths: ["60%", "40%"],
                    body: [
                        [
                            {
                                border: [false, false, false, false],
                                text: title,
                                style: "mainTitle",
                            },
                            {
                                border: [false, false, false, false],
                                text: documentNumber,
                                style: "documentNumber",
                            },
                        ],
                        [
                            {
                                border: [false, false, false, false],
                                text: create_filter(filters),
                                fontSize: 9,
                                bold: true,
                            },

                            {
                                border: [false, false, false, false],
                                text: "(All)",
                                alignment: "right",
                                fontSize: 10,
                            },
                        ],
                    ],
                },
            },


            generate_pdf_body(columnTitles, columnWidths, rows),
        ],
        defaultStyle: {
            font: "Liberation",
        },

        styles: {
            topCompanyName: {
                fontSize: 20,
                alignment: "center",
                fontWeight: "bold",
            },

            topBarTable: {
                color: "#2b2b2b",
                fontSize: 10,
            },

            headerText: {
                fontSize: 13,
                bold: true,
                color: "#ffffff",
                opacity: 1,
                margin: [10, 0, 0, 0],
            },
            footerText: {
                fontSize: 13,
                bold: true,
                color: "rgba(18,18,18,0.9)",
                opacity: 0.6,
            },
            headerCols: {
                background: "red",
            },
            headerTable: {
                border: false,
            },
            logo: {
                italics: true,
                alignment: "center",
                background: "red",
                marginTop: 10,
            },

            companyName: {
                fontSize: 13,
                bold: true,
            },
            mainTitle: {
                fontSize: 13,
                bold: true,
            },

            documentNumber: {
                fontSize: 11,
                bold: true,
                alignment: "right",
            },

            contentTable: {
                fontSize: 11,
                margin: [15, 5, 15, 15],
            },

            footerRow: {
                marginTop: 5,
            },

            tableHead: {
                bold: true,
                fontSize: 11,
            },

            summaryTable: {
                marginTop: 20,
            },

            copyrightText: {
                color: "gray",
                fontSize: 10,
                width: "*",
                alignment: "right",
                marginRight: 10,
                italics: true,
            },

            tableCol: {
                lineHeight: 1.5,
                alignment: "justify",
                marginTop: 8,
                fontSize: 9,
                marginLeft: 5,
            },
            chart: {
                alignment: "justify",
                background: "red",
                marginTop: 10,
                marginLeft: 10,
            },
            reportValue: {
                fontSize: 9,
                margin: 1
            },
             footerValue: {
                fontSize: 10,
                margin: 1,
                bold:true,
                color:"purple"

            },

            icon: { font: "Fontello" },
        },
    };

    //  pdfMake.createPdf(docDefinition).open({}, window);
    pdfMake.createPdf(docDefinition).open();
};
