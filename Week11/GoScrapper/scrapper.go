package main

import (
	"encoding/json"
	"fmt"
	"log"
	"os"

	"github.com/gocolly/colly"
)

type SiteConfig struct {
	Site             string `json:"site"`
	NextPageSelector string `json:"nextPageSelector"`
	Data             string `json:"data"`
	Title            string `json:"title"`
}

type dataConfig struct {
	Title string
	Data  string
}

func main() {
	var sites []SiteConfig

	readFile, err := os.Open("sites.json")
	if err != nil {
		panic(fmt.Sprintf("Json dosyası açılırken bir hata oluştu: %s", err))
	}
	defer readFile.Close()

	if err := json.NewDecoder(readFile).Decode(&sites); err != nil {
		panic(fmt.Sprintf("Json dosyası okunamadı: %s", err))
	}

	c := colly.NewCollector()

	scrapedData := []dataConfig{}
	for _, site := range sites {
		fmt.Printf("Scraping site: %s\n", site.Site)

		pageCounter := 0
		const maxPages = 1

		c.OnHTML(site.NextPageSelector, func(e *colly.HTMLElement) {
			if pageCounter >= maxPages {
				fmt.Println("Reached page limit. Stopping further visits.")
				return
			}
			nextPage := e.Attr("href")
			if nextPage != "" {
				fullURL := e.Request.AbsoluteURL(nextPage)
				fmt.Println("Visiting Next Page:", fullURL)
				pageCounter++
				e.Request.Visit(fullURL)
			}
		})
		c.OnHTML("html", func(e *colly.HTMLElement) {
			currentTitle := e.ChildText(site.Title)
			currentData := e.ChildText(site.Data)

			scrapedData = append(scrapedData, dataConfig{
				Title: currentTitle,
				Data:  currentData,
			})
		})

		c.OnError(func(r *colly.Response, err error) {
			log.Println("Request URL:", r.Request.URL, "failed with error:", err)
		})
		c.Visit(site.Site)
	}
	writeFile, _ := os.OpenFile("data.json", os.O_WRONLY, os.ModePerm)
	defer writeFile.Close()
	encoder := json.NewEncoder(writeFile)
	encoder.SetEscapeHTML(false)
	encoder.Encode(scrapedData)

	fmt.Println("Scraped data:", scrapedData)
}
