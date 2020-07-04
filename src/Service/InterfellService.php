<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Cliente;
use App\Entity\Billetera;


class InterfellService
{ 
    private $em;
    private $session;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session )
    {
        $this->em = $entityManager;
        $this->session = $session;
    }

    public function registrarCliente($nombres, $email, $celular, $documento){

        $cliente = new Cliente();
        $cliente->setNombres($nombres);
        $cliente->setEmail($email);
        $cliente->setCelular($celular);
        $cliente->setDocumento($documento);

        $this->em->persist($cliente);
        $this->em->flush();
        
        return "Cliente creado satisfactoriamente";

    }

        public function recargarBilletera($monto, $celular, $documento){

        $repoCliente = $this->em->getRepository(Cliente::class);
        $repoBilletera = $this->em->getRepository(Billetera::class);
        
        // Se busca cliente en la bd
        $cliente = $repoCliente->findOneBy([
            'Documento' => $documento,
            'Celular' => $celular,
        ]);

        if($cliente == null){
            return "Cliente no encontrado";
        }

        // Se busca la billetera del cliente

        $billetera = $repoBilletera->findOneBy([
            'Cliente' => $cliente->getId()
        ]);

        // Si existe la billetera se suma o resta el monto
        // de lo contrario se crea la billetera para el cliente

        if($billetera){
            $billetera->setSaldo($billetera->getSaldo() + $monto);
        } else {
            $billetera = new Billetera();
            $billetera->setCliente($cliente);
            $billetera->setSaldo($monto);
        }
        $this->em->persist($billetera);
        $this->em->flush();
        return "Billetera recargada satisfactoriamente";

    }

     public function pagarCompra($monto, $documento){
        $repositoryCliente = $this->em->getRepository(Cliente::class);
        $this->session->start();
        $sessionId = $this->session->getId();

        // token aleatorio de 6 digitos
        $token = mt_rand(100000,999999); 
    
        $cliente = $repositoryCliente->findOneBy([
            'Documento' => $documento
        ]);
        if($cliente == null){
            return "Cliente no encontrado";
        }

        $emailCliente = $cliente->getEmail();

        $this->session->set($token, $sessionId);
        $this->session->set("documento", $documento);
        $this->session->set("monto",$monto);

        return "session: " . $sessionId . " token: " . $token;

    }

        public function confirmarPago($idSesion,$token){

        $repoCliente = $this->em->getRepository(Cliente::class);

        // obtiene sesion previa

        $sesionPrevia = $this->session->get($token);

        // compara sesion previa con id recibida
        if($sesionPrevia == $idSesion){
            $documento = $this->session->get("documento");
            $monto = $this->session->get("monto");
            
            $cliente = $repoCliente->findOneBy([
                'Documento' => $documento
            ]);

            if($cliente == null){
                return "Cliente no encontrado";
            }
            $billetera = $repoBilletera->findOneBy([
                'Cliente' => $cliente->getId()
            ]);
            if($billetera){
                $billetera->setSaldo($billetera->getSaldo() - $monto);
            }
            $this->em->persist($billetera);
            $this->em->flush();

            return "Pago confirmado!!";
        }
        return "Pago no confirmado!!";

     }

        public function consultarSaldo($celular, $documento)
        {

        $repoCliente = $this->em->getRepository(Cliente::class);
        $repoBilletera = $this->em->getRepository(Billetera::class);

        // busca cliente en la bd
        $cliente = $repoCliente->findOneBy([
            'Documento' => $documento,
            'Celular' => $celular
        ]);
        if($cliente == null){
            return "Cliente no encontrado";
        }
        // busca billetera asociada con el cliente

        $billetera = $repoBilletera->findOneBy([
            'Cliente' => $cliente->getId()
        ]);
        if($billetera){
            return ($billetera->getSaldo());
        }
        return "No existe billetera para el cliente";

        }

}