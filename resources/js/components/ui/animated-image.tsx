import { motion } from "framer-motion";
import { cn } from "@/lib/utils";

interface AnimatedImageProps {
    src: string;
    alt: string;
    className?: string;
    containerClassName?: string;
}

export function AnimatedImage({ src, alt, className, containerClassName }: AnimatedImageProps) {
    return (
        <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            whileInView={{ opacity: 1, scale: 1 }}
            viewport={{ once: true }}
            transition={{ duration: 0.7 }}
            className={cn("relative overflow-hidden rounded-xl bg-muted", containerClassName)}
        >
            <motion.img
                src={src}
                alt={alt}
                whileHover={{ scale: 1.05 }}
                transition={{ duration: 0.4, ease: "easeOut" }}
                className={cn("h-full w-full object-cover", className)}
            />
        </motion.div>
    );
}
