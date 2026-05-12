import { motion } from "framer-motion"
import { cn } from "@/lib/utils"

function Spinner({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div className={cn("relative flex items-center justify-center", className)} {...props}>
      <motion.div
        className="absolute inset-0 border-2 border-amber-500/20 rounded-full"
        animate={{ rotate: 360 }}
        transition={{ repeat: Infinity, duration: 2, ease: "linear" }}
      />
      <motion.div
        className="absolute inset-0 border-t-2 border-amber-500 rounded-full"
        animate={{ rotate: 360 }}
        transition={{ repeat: Infinity, duration: 1, ease: "linear" }}
      />
      <motion.div
        className="w-1 h-1 bg-amber-500 rounded-full"
        animate={{ scale: [1, 1.5, 1] }}
        transition={{ repeat: Infinity, duration: 1 }}
      />
    </div>
  )
}

export { Spinner }
