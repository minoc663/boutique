<?php 

 namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

 class EasyAdminSubscriber implements EventSubscriberInterface
 {  
   private $appkernel;

   public function __construct(KernelInterface $appkernel)
   {
      $this->appkernel = $appkernel;
   }
    public static function getSubscribedEvents()
    {
      return[
          
        BeforeEntityPersistedEvent::class => ['setIllustration'],
      ];
    }

    public function setIllustration(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();
        $tmp_name = $entity->getIllustration();
        $filename = uniqid();
        
        
        $extension = pathinfo($_FILES['Product']['name']['illustration']['file'], PATHINFO_EXTENSION);
        
        $project_dir = $this->appkernel->getProjectDir();

        move_uploaded_file($tmp_name,$project_dir.'/public/uploads/'.$filename.'.'.$extension);

        $entity->setIllustration($filename.'.'.$extension);
        
    }
 }
