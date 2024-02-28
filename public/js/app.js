$(document).ready(function () {
    window["show_notif"] = function (title, status, message) {

        var icon = 'fa';
        switch (status) {
            case "success":
                icon += ' fa-check';
                break;
            case "error":
                icon += ' fa-exclamation';
                break;
            case "success":
                icon += ' fa-exclamation';
                break;
        }
        var titleVar = '';
        if (title !== '')
            titleVar = '<strong>' + title + '</strong>, ';
        $.notify({
            title: titleVar,
            icon: icon,
            message: message
        }, {
            type: status,
            animate: {
                enter: 'animated fadeInUp',
                exit: 'animated fadeOutRight'
            },
            placement: {
                from: "top",
                align: "left"
            },
            offset: 20,
            spacing: 10,
            z_index: 1031,
            allow_dismiss: true,
            delay: 1000,
            timer: 3000,
        });
    }

    window["copyToClipboard"] = function (text) { 
        if (navigator.clipboard) {
            
            navigator.clipboard.writeText(text)
                .then(function() {
                    console.log('Text copied to clipboard');
                })
                .catch(function(err) {
                    console.error('Unable to copy text to clipboard:', err);
                });
        } else {
            // Create a new textarea element
            var textarea = document.createElement('textarea');
            // Set the value of the textarea to the text to be copied
            textarea.value = text;
            // Make the textarea invisible
            textarea.style.position = 'fixed';
            textarea.style.left = '-9999px';
            textarea.style.top = '-9999px';
            
            // Append the textarea to the document body
            document.body.appendChild(textarea);
            
            // Select the text within the textarea
            textarea.select();
            
            try {
                // Execute the copy command
                document.execCommand('copy');
            } catch (err) {
                console.error('Unable to copy text to clipboard:', err);
            }
            
            // Remove the textarea from the document body
            document.body.removeChild(textarea);
        }
    }

    window["formatDate"] = function (date) {
        var d = new Date(date),
          month = '' + (d.getMonth() + 1),
          day = '' + d.getDate(),
          year = d.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [year, month, day].join('-');
    }
    
    
    window["inputDate"] = function (date) {
        var date = date.replaceAll('-', '');
        date = date.replaceAll('D', '');
        date = date.replaceAll('M', '');
        date = date.replaceAll('Y', '');
        var long = date.length;

        var day = '';
        var month = '';
        var year = '';
        if (long > 0)
            day = date[0];
        if (long > 1) {
            if (day > 3 && day < 10) {
                day = '0' + day;
                month += date[1];
            } else {
                day += date[1];
            }
        }
        if (long > 2) {
            month += date[2];
        }
        if (long > 3) {
            if (month > 1 && month < 10) {
                month = '0' + month;
                year += date[3];
            } else {
                month += date[3];
            }
        }
        if (long > 4)
            year += date[4];
        if (long > 5)
            year += date[5];
        if (long > 6)
            year += date[6];
        if (long > 7)
            year += date[7];



        if (long > 7) {
            var control = new Date(year, month, day);
            if (control.getFullYear() < 1940 || control.getFullYear() > 2030 || control.getFullYear() != year) {
                alert("por favor ingrese una fecha válida del formato Día-Mes-Año");
                return null;
            }


            if (control.getMonth() > 12 || control.getMonth() != month) {
                alert("por favor ingrese una fecha válida del formato Día-Mes-Año");
                return null;
            }

            if (control.getDate() > 31 || control.getDate() != day) {
                alert("por favor ingrese una fecha válida del formato Día-Mes-Año");
                return null;
            }
        }

        var newDate = day;
        if (month != '')
            newDate += '-' + month;
        if (year != '')
            newDate += '-' + year;
        return newDate;
    }
    
    

    window["formatterEuro"] = new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0
    })

});

