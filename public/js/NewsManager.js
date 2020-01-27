class NewsManager
{
    constructor()
    {
        this.news = [];
        this.Title = document.querySelector("#News .article h1");
        this.Image = document.querySelector("#News .article img");
        this.Text = document.querySelector("#News .article p");
        
        this.articleBox = [];
        this.sourceChild = [];
        this.titleChild = [];
        this.imageChild = [];
        this.dateChild = [];
        this.contentChild = [];

        this.icon = document.getElementById("Icon");
        this.actualité = document.getElementById("Actualité");
        this.sport = document.getElementById("Sport");
        this.divertissement = document.getElementById("Divertissement");
        this.santé = document.getElementById("Santé");
        this.science = document.getElementById("Science");
        this.technologie = document.getElementById("Technologie");

    }

    GetNews(url)
    {
        fetch(url)
        .then( (response) => {
            return response.json();
        })
        .then ((data) => {
            this.StockData(data);
            return data;
        })
        .then( (data) => {
            this.ShowNews();
            return data;
        })
        .catch ( (err) => {
            console.log(err);
        })
    }

    StockData(data)
    {
        for(let i = 0; i < data.totalResults; i++)
        {
            this.news.push(data.articles[i]);
        }
    }

    ShowNews()
    {
        for (let i = 0; i < this.news.length; i++) 
        {
            this.articleBox[i] = document.createElement("div");
            this.sourceChild[i] = document.createElement("h1");
            this.titleChild[i] = document.createElement("h2");
            this.imageChild[i] = document.createElement("img");
            this.dateChild[i] = document.createElement("h3");
            this.contentChild[i] = document.createElement("p");

            this.sourceChild[i].innerHTML = this.news[i].source.name;

            this.titleChild[i].innerHTML = "<a href=" + this.news[i].url + ">" + this.news[i].title + "</a>";
            this.dateChild[i].innerHTML = "Date de publication : " + this.news[i].publishedAt;
            
            if(this.news[i].urlToImage == null)
            {
                this.imageChild[i].setAttribute("src", "../image/news.png");
            }
            else
            {
                this.imageChild[i].setAttribute("src", this.news[i].urlToImage);
                this.imageChild[i].setAttribute("alt", "Absence d'image");
            }

            if (this.news[i].description == "" || this.news[i].description == null) 
            {
                this.contentChild[i].innerHTML = '<h2>' + 'Pas de description disponible' + '</h2>';
            }
            else
            {
                this.contentChild[i].innerHTML =  this.news[i].description;
            }

            document.querySelector("#News #Article_Box").appendChild(this.articleBox[i]);
            this.articleBox[i].setAttribute("class", "article");
            this.articleBox[i].appendChild(this.sourceChild[i]);
            this.articleBox[i].appendChild(this.titleChild[i]);
            this.articleBox[i].appendChild(this.imageChild[i]);
            this.articleBox[i].appendChild(this.contentChild[i]);
            this.articleBox[i].appendChild(this.dateChild[i]);
        }
    }

    SessionStorage()
    {
        this.icon.addEventListener("click", () => {
            sessionStorage.removeItem("category");
        })
        this.actualité.addEventListener("click", () => {
            sessionStorage.removeItem("category");
            
        })
        this.sport.addEventListener("click", () => {
            sessionStorage.setItem("category", "sports");
        })
        this.divertissement.addEventListener("click", () => {
            sessionStorage.setItem("category", "entertainment");
        })
        this.santé.addEventListener("click", () => {
            sessionStorage.setItem("category", "health");
        })
        this.science.addEventListener("click", () => {
            sessionStorage.setItem("category", "science");
        })
        this.technologie.addEventListener("click", () => {
            sessionStorage.setItem("category", "technology");   
        })
    }
}

let news = new NewsManager();
news.SessionStorage();

if(sessionStorage.getItem("category") == null)
{
    news.GetNews('https://newsapi.org/v2/top-headlines?country=fr&apiKey=a727be71caab4420b0862dad9c71c661');
}
else
{
    news.GetNews('https://newsapi.org/v2/top-headlines?country=fr&category=' + sessionStorage.getItem("category") + '&apiKey=a727be71caab4420b0862dad9c71c661');
}



