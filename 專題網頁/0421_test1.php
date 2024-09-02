<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>搜尋資料藍圖</title>
    <style>
        body {
            background-color: #f0f8ff; /* 淡藍色背景 */
            background-repeat: no-repeat;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        #pageTitle {
            text-align: center;
            margin: 20px 0;
        }
        #searchFormContainer {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }
        #searchFormContainer form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        #searchFormContainer select,
        #searchFormContainer button {
            border-radius: 15px;
            padding: 8px 12px;
            margin: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        #searchFormContainer button {
            padding: 8px 20px;
            background-color: #f2f2f2;
            cursor: pointer;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            word-break: break-word; 
        }
        th {
            background-color: #f2f2f2;
        }
        #previewArea {
            margin-top: 20px;
            text-align: center;
        }
        #previewArea img,
        #previewArea iframe {
            max-width: 90%;
            max-height: 500px;
            border: 1px solid #ccc;
        }

        /* 响应式设计 */
        @media (max-width: 768px) {
            #searchFormContainer {
                flex-direction: column;
                align-items: stretch;
            }
            #searchFormContainer select,
            #searchFormContainer button {
                width: 100%;
                box-sizing: border-box;
                margin: 5px 0;
            }
            table {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            table {
                font-size: 12px;
            }
            #searchFormContainer select,
            #searchFormContainer button {
                padding: 6px 10px;
            }
            #previewArea img,
            #previewArea iframe {
                max-height: 300px;
            }
        }
    </style>
</head>
<body>
    <div id="pageTitle">
        <h1>搜尋資料藍圖</h1>
    </div>
    <div id="searchFormContainer">
        <form id="searchForm">
            <select id="searchTerm1" name="searchTerm1">
                <option value="" disabled selected>請選擇標號</option>
                <option value="IQVU03">IQVU03</option>
            </select>
            <select id="searchTerm2" name="searchTerm2">
                <option value="" disabled selected>請選擇圖號</option>
                <!-- 圖號選項會由 PHP 生成 -->
            </select>
            <button type="submit">搜尋</button>
        </form>
    </div>
    
    <div id="searchResults"></div>

    <div id="previewArea"></div>

    <script>
        // 使用 fetch API 來獲取圖號選項並填充下拉選單
        document.addEventListener('DOMContentLoaded', function() {
            fetch('getNumbers.php')
                .then(response => response.json())
                .then(data => {
                    const numberSelect = document.getElementById('searchTerm2');
                    numberSelect.innerHTML = '<option value="" disabled selected>請選擇圖號</option>'; // 保留預設選項

                    data.forEach(number => {
                        const option = document.createElement('option');
                        option.value = number;
                        option.textContent = number;
                        numberSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching number options:', error));
        });

        document.getElementById('searchForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var searchTerm1 = document.getElementById('searchTerm1').value;
            var searchTerm2 = document.getElementById('searchTerm2').value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "0421_1.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);
                    displaySearchResults(data);
                }
            };
            xhr.send("searchTerm1=" + searchTerm1 + "&searchTerm2=" + searchTerm2);
        });

        function displaySearchResults(data) {
            var resultsDiv = document.getElementById('searchResults');
            resultsDiv.innerHTML = '';

            if (!data || data.length === 0) {
                resultsDiv.innerHTML = '<p>無搜尋結果</p>';
                return;
            }

            // 排序數據
            data.sort(function(a, b) {
                var fileA = a.file.toUpperCase(); // 忽略大小寫
                var fileB = b.file.toUpperCase();
                return fileA < fileB ? -1 : fileA > fileB ? 1 : 0;
            });

            var table = document.createElement('table');
            var headerRow = table.insertRow();
            headerRow.insertCell().textContent = "編號";
            headerRow.insertCell().textContent = "標號";
            headerRow.insertCell().textContent = "圖號";
            headerRow.insertCell().textContent = "版次";
            headerRow.insertCell().textContent = "類別";
            headerRow.insertCell().textContent = "圖名";
            headerRow.insertCell().textContent = "CAD檔名";
            headerRow.insertCell().textContent = "影像檔名";
            headerRow.insertCell().textContent = "url";

            var index = 1;
            for (var i = 0; i < data.length; i++) {
                var row = table.insertRow();
                var item = data[i];
                row.insertCell().textContent = index++;
                row.insertCell().textContent = item.serial;
                row.insertCell().textContent = item.number;
                row.insertCell().textContent = item.edition;
                row.insertCell().textContent = item.category;
                row.insertCell().textContent = item.image;
                row.insertCell().textContent = item.CAD;
                row.insertCell().textContent = item.file;

                // URL的超連結
                var urlCell = row.insertCell();
                var urlLink = document.createElement('a');
                urlLink.href = 'view_pdf.php?fileUrl=' + encodeURIComponent(item.url); // 使用 PHP 來顯示 PDF
                urlLink.target = "_blank"; // 在新標籤頁打開
                urlLink.textContent = '圖片'; 
                urlCell.appendChild(urlLink);
            }

            resultsDiv.appendChild(table);
        }
    </script>
</body>
</html>
